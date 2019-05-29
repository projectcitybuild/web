<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Repositories\AccountRepository;
use App\Entities\Accounts\Repositories\UnactivatedAccountRepository;
use App\Entities\Accounts\Notifications\AccountActivationNotification;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Hash;
use App\Http\WebController;
use App\Entities\Accounts\Models\Account;
use Illuminate\Support\Facades\DB;
use App\Library\Discourse\Authentication\DiscourseLoginHandler;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

final class RegisterController extends WebController
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var UnactivatedAccountRepository
     */
    private $unactivatedAccountRepository;

    /**
     * @var DiscourseLoginHandler
     */
    private $discourseLoginHandler;

    /**
     * @var CLient
     */
    private $client;


    public function __construct(
        AccountRepository $accountRepository,
        UnactivatedAccountRepository $unactivatedAccountRepository,
        DiscourseLoginHandler $discourseLoginHandler,
        Client $client
    ) {
        $this->accountRepository = $accountRepository;
        $this->unactivatedAccountRepository = $unactivatedAccountRepository;
        $this->discourseLoginHandler = $discourseLoginHandler;
        $this->client = $client;
    }

    public function showRegisterView()
    {
        return view('front.pages.register.register');
    }

    public function register(RegisterRequest $request)
    {
        $input = $request->validated();

        $email      = $input['email'];
        $password   = $input['password'];
        $password   = Hash::make($password);

        $unactivatedAccount = $this->unactivatedAccountRepository->create($email, $password);
        $unactivatedAccount->notify(new AccountActivationNotification($unactivatedAccount));

        return view('front.pages.register.register-success');
    }

    /**
     * Finishes the registration process by attempting to activate the account that 
     * belongs to the given email address
     *
     * @param Request $request
     *
     * @return View
     * @throws \Exception
     */
    public function activate(Request $request)
    {
        // payload is signed to prevent tampering, so this URL parameter is safe to use as it is
        $email = $request->get('email');
        
        if (empty($email)) 
        {
            return view('front.pages.register.register');
        }

        $unactivatedAccount = $this->unactivatedAccountRepository->getByEmail($email);
        if ($unactivatedAccount === null) 
        {
            // TODO: inform user that account does not exist
            abort(404, 'Account does not exist');
        }

        $account = $this->accountRepository->getByEmail($email);
        if ($account) 
        {
            // TODO: inform user account is already activated
            abort(410, 'Account already activated');
        }

        DB::beginTransaction();
        try 
        {
            $account = $this->accountRepository->create(
                $unactivatedAccount->email,
                $unactivatedAccount->password,
                $request->ip(),
                Carbon::now()
            );
            $unactivatedAccount->delete();

            DB::commit();
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            throw $e;
        }

        // Login to Discourse on behalf of the user to force Discourse to create an account for the user. 
        // This step is necessary because some users register an account but never login once afterwards, 
        // which means they'll have a PCB account but not a forum account, and this causes all sorts of 
        // nasty problems later.
        //
        // This step doesn't actually log the user in, because the server is making the request on behalf
        // of the user.
        $this->forceLoginServerSide($request, $account);

        return view('front.pages.register.register-verify-complete');
    }

    /**
     * Logs-in to Discourse on behalf of the user, server-side
     *
     * @param Request $request
     * @param Account $account
     * @return void
     */
    private function forceLoginServerSide(Request $request, Account $account)
    {
        if (Environment::isDev())
        {
            return;
        }
        try 
        {
            $endpoint = $this->discourseLoginHandler->getRedirectUrl(
                $request,
                $account->getKey(), 
                $account->email
            );
            
            $this->client->get($endpoint);
        } 
        catch (BadSSOPayloadException $e) 
        {
            Log::debug('Missing nonce or return key in session', ['session' => $request->session()]);
            throw $e;
        }
    }
}
