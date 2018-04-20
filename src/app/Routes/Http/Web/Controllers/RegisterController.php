<?php

namespace App\Routes\Http\Web\Controllers;

use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Accounts\Repositories\UnactivatedAccountRepository;
use App\Routes\Http\Web\WebController;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Contracts\Validation\Factory as Validation;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Hash;
use App\Modules\Accounts\Notifications\AccountActivationNotification;
use GuzzleHttp\Client;

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

    public function register(Request $request, Client $client) {
        $validator = $this->validation->make($request->all(), [
            'email'                 => 'required|email|unique:accounts,email',
            'password'              => 'required|min:8',    // discourse min is 8 or greater
            'password_confirm'      => 'required_with:password|same:password',
            'g-recaptcha-response'  => 'required',
        ], [
            'g-recaptcha-response' => 'reCAPTCHA verificatiosn failed'
        ]);

        $validator->after(function($validator) use($request, $client) {
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'    => env('RECAPTCHA_SECRET_KEY'),
                    'response'  => $request->get('g-recaptcha-response'),
                    'remoteip'  => $request->ip(),
                ],
            ]);
            $result = json_decode($response->getBody(), true);

            $success = $result['success'];
            if($success === null || $success === false) {
                $validator->errors()->add('g-recaptcha-response', 'reCAPTCHA failed. Are you a bot?');
            }
        });

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

        $unactivatedAccount = $this->unactivatedAccountRepository->create(
            $email, 
            $password
        );
        
        $unactivatedAccount->notify(new AccountActivationNotification($unactivatedAccount));

        return view('register-success');
    }

    /**
     * Attempts to activate an account via token
     *
     * @param Request $request
     * @return void
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
            $account = $this->accountRepository->create(
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
