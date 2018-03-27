<?php

namespace App\Routes\Http\Web\Controllers;

use App\Modules\Discourse\Services\Authentication\DiscourseAuthService;
use App\Modules\Forums\Exceptions\BadSSOPayloadException;
use App\Modules\Accounts\Services\AccountLinkService;
use App\Routes\Http\Web\WebController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\Factory as Validation;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Factory;
use App\Modules\Accounts\Repositories\AccountRepository;
use App\Modules\Accounts\Repositories\AccountPasswordResetRepository;
use App\Modules\Accounts\Mail\AccountPasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Connection;
use Hash;

class PasswordRecoveryController extends WebController {

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var AccountPasswordResetRepository
     */
    private $passwordResetRepository;

    /**
     * @var Connection
     */
    private $connection;


    public function __construct(
        AccountRepository $accountRepository, 
        AccountPasswordResetRepository $passwordResetRepository,
        Connection $connection
    ) {
        $this->accountRepository = $accountRepository;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->connection = $connection;
    }

    public function showEmailForm(Request $request) {
        return view('password-reset');
    }

    public function sendVerificationEmail(Request $request, Factory $validation) {
        $validator = $validation->make($request->all(), [
            'email' => 'email|required',
        ]);

        $email = $request->get('email');
        $account = null;
        $validator->after(function($validator) use($account, $email) {
            $account = $this->accountRepository->getByEmail($email);

            if($account === null) {
                $validator->errors()->add('email', 'No account belongs to the given email address');
            }
        });

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $token = hash_hmac('sha256', time(), env('APP_KEY'));
        $passwordReset = $this->passwordResetRepository->updateOrCreateByEmail($email, $token);

        Mail::to($email)->queue(new AccountPasswordResetMail($passwordReset));

        // TODO: redirect
    }

    public function showResetForm(Request $request) {
        // make sure token has not been invalidated
        $token = $request->get('token');
        if($token === null) {
            abort(401, "No token provided");
        }

        $passwordReset = $this->passwordResetRepository->getByToken($token);
        if($passwordReset === null) {
            abort(401, "Token invalid or has expired");
        }

        return view('password-reset-form', [
            'passwordToken' => $request->get('token')
        ]);
    }

    public function resetPassword(Request $request, Factory $validation) {
        $validator = $validation->make($request->all(), [
            'password_token'    => 'required',
            'password'          => 'required|min:4',
            'password_confirm'  => 'required_with:password|same:password',
        ]);

        if($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $passwordReset = $this->passwordResetRepository->getByToken($request->get('password_token'));
        if($passwordReset === null) {
            abort(401, "Password token not found");
        }

        $account = $this->accountRepository->getByEmail($passwordReset->email);
        if($account === null) {
            abort(401, "Account not found");
        }

        $this->connection->beginTransaction();
        try {
            $account->password = Hash::make($request->get('password'));
            $account->save();

            $passwordReset->delete();
            $this->connection->commit();

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }

        // TODO: redirect
    }

}
