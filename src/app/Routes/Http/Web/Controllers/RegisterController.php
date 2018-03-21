<?php

namespace App\Routes\Http\Web\Controllers;

use Illuminate\Support\Facades\View;
use App\Routes\Http\Web\WebController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as Validation;
use App\Modules\Accounts\Repositories\AccountRepository;
use Hash;
use App\Modules\Accounts\Repositories\AccountActivationCodeRepository;
use Illuminate\Support\Facades\Mail;
use App\Modules\Accounts\Mail\AccountActivationMail;
use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Contracts\Auth\Guard as Auth;

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
     * @var AccountActivationCodeRepository
     */
    private $codeRepository;

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
        AccountActivationCodeRepository $codeRepository,
        Connection $connection,
        Auth $auth
    ) {
        $this->validation = $validation;
        $this->accountRepository = $accountRepository;
        $this->codeRepository = $codeRepository;
        $this->connection = $connection;
        $this->auth = $auth;
    }

    public function showRegisterView() {
        return view('register');
    }

    public function register(Request $request) {
        $validator = $this->validation->make($request->all(), [
            'email'             => 'required|email|unique:accounts,email',
            'password'          => 'required|min:4',
            'password_confirm'  => 'required_with:password|same:password',
        ]);

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator->errors())
                ->withInput();
        }

        $email = $request->get('email');
        $password = $request->get('password');
        $password = Hash::make($password);

        $salt = env('APP_KEY');
        $token = hash_hmac('sha256', time().$email, $salt);

        $activationCode = $this->codeRepository->create(
            $token,
            $email,
            $password,
            (Carbon::now())->addDays(5)
        );

        Mail::to($email)->queue(new AccountActivationMail($activationCode));
    }

    /**
     * Attempts to activate an account via token
     *
     * @param Request $request
     * @return void
     */
    public function activate(Request $request) {
        $token = $request->get('token');
        if($token === null || empty($token)) {
            abort(401);
        }

        $activationCode = $this->codeRepository->getByToken($token);
        if($activationCode === null) {
            // TODO: inform user token has most likely expired
            abort(410);
        }
        
        if(Carbon::now()->gt($activationCode->expires_at)) {
            // TODO: inform user token has expired
            abort(410);
        }

        if($activationCode->is_used) {
            // TODO: inform user token has been used
            abort(410);
        }

        $accountByEmail = $this->accountRepository->getByEmail($request->get('email'));
        if($accountByEmail) {
            // TODO: inform user account is already activated
            abort(410);
        }

        $this->connection->beginTransaction();
        try {
            $account = $this->accountRepository->create(
                $activationCode->email,
                $activationCode->password,
                $request->ip()
            );

            $activationCode->is_used = true;
            $activationCode->save();

            $this->connection->commit();

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        

    }
}
