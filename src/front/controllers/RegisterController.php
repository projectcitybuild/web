<?php

namespace Front\Controllers;

use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Accounts\Repositories\UnactivatedAccountRepository;
use App\Modules\Recaptcha\RecaptchaRule;
use App\Modules\Recaptcha\RecaptchaService;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Contracts\Validation\Factory as Validation;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Hash;
use App\Modules\Accounts\Notifications\AccountActivationNotification;
use Illuminate\View\View;

class RegisterController extends WebController {
    
    /**
     * @var Validation
     */
    private $validation;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var UnactivatedAccountRepository
     */
    private $unactivatedAccountRepository;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(
        Validation $validation, 
        AccountRepository $accountRepository,
        UnactivatedAccountRepository $unactivatedAccountRepository,
        Connection $connection,
        Auth $auth
    ) {
        $this->validation = $validation;
        $this->accountRepository = $accountRepository;
        $this->unactivatedAccountRepository = $unactivatedAccountRepository;
        $this->connection = $connection;
        $this->auth = $auth;
    }

    public function showRegisterView() {
        return view('register');
    }

    public function register(Request $request, RecaptchaService $recaptcha) {
        $validator = $this->validation->make($request->all(), [
            'email'                 => 'required|email|unique:accounts,email',
            'password'              => 'required|min:8',    // discourse min is 8 or greater
            'password_confirm'      => 'required_with:password|same:password',
            'g-recaptcha-response'  => ['required', resolve(RecaptchaRule::class)],
        ]);

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator->errors())
                ->withInput();
        }

        $email      = $request->get('email');
        $password   = $request->get('password');
        $password   = Hash::make($password);

        $unactivatedAccount = $this->unactivatedAccountRepository->create($email, $password);
        $unactivatedAccount->notify(new AccountActivationNotification($unactivatedAccount));

        return view('register-success');
    }

    /**
     * Attempts to activate an account via token
     *
     * @param Request $request
     *
     * @return View
     * @throws \Exception
     */
    public function activate(Request $request) {
        $email = $request->get('email');
        if($email === null || empty($email)) {
            abort(401);
        }

        $unactivatedAccount = $this->unactivatedAccountRepository->getByEmail($email);
        if($unactivatedAccount === null) {
            // TODO: inform user that account does not exist
            abort(404);
        }

        $accountByEmail = $this->accountRepository->getByEmail($email);
        if($accountByEmail) {
            // TODO: inform user account is already activated
            abort(410);
        }

        $this->connection->beginTransaction();
        try {
            $this->accountRepository->create(
                $unactivatedAccount->email,
                $unactivatedAccount->password,
                $request->ip(),
                Carbon::now()
            );

            $unactivatedAccount->delete();

            $this->connection->commit();

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        return view('register-verify-complete');
    }
}
