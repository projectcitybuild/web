<?php

namespace Front\Controllers;

use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Accounts\Repositories\UnactivatedAccountRepository;
use App\Modules\Accounts\Notifications\AccountActivationNotification;
use App\Modules\Recaptcha\RecaptchaRule;
use Front\Requests\RegisterRequest;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Contracts\Validation\Factory as Validation;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Hash;

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

    public function register(RegisterRequest $request) {
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
