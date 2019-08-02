<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Repositories\AccountRepository;
use App\Entities\Accounts\Repositories\AccountPasswordResetRepository;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordEmailRequest;
use Illuminate\Http\Request;
use Hash;
use App\Http\WebController;
use App\Http\Actions\AccountPasswordReset\SendPasswordResetEmail;
use App\Http\Actions\AccountPasswordReset\ResetAccountPassword;
use App\Exceptions\Http\NotFoundException;

final class PasswordRecoveryController extends WebController
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var AccountPasswordResetRepository
     */
    private $passwordResetRepository;


    public function __construct(AccountRepository $accountRepository, AccountPasswordResetRepository $passwordResetRepository) 
    {
        $this->accountRepository = $accountRepository;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    public function showEmailForm()
    {
        return view('front.pages.password-reset.password-reset');
    }

    public function sendVerificationEmail(SendPasswordEmailRequest $request, SendPasswordResetEmail $sendPasswordResetEmail)
    {
        $input = $request->validated();
        $email = $input['email'];

        $sendPasswordResetEmail->execute(
            $request->getAccount(),
            $email
        );

        return redirect()->back()->with(['success' => $email]);
    }

    public function showResetForm(Request $request)
    {
        $token = $request->get('token');
        if ($token === null) {
            abort(401, "No token provided");
        }

        $passwordReset = $this->passwordResetRepository->getByToken($token);
        if ($passwordReset === null) {
            abort(401, "Token invalid or has expired");
        }

        return view('front.pages.password-reset.password-reset-form', [
            'passwordToken' => $request->get('token')
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request, ResetAccountPassword $resetAccountPassword)
    {
        $input = $request->validated();

        try {
            $resetAccountPassword->execute(
                $input['password_token'],
                $input['password']
            );
        } catch(NotFoundException $e) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', $e->getMessage());
        }
        
        return view('front.pages.password-reset.password-reset-success');
    }
}
