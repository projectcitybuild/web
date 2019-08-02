<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordEmailRequest;
use App\Http\WebController;
use App\Entities\Accounts\Models\AccountPasswordReset;
use App\Http\Actions\AccountPasswordReset\SendPasswordResetEmail;
use App\Http\Actions\AccountPasswordReset\ResetAccountPassword;
use App\Exceptions\Http\NotFoundException;
use Illuminate\Http\Request;

final class PasswordResetController extends WebController
{
    /**
     * Shows the form to send a verification URL to the user's email address
     *
     * @return void
     */
    public function create()
    {
        return view('front.pages.password-reset.password-reset');
    }

    /**
     * Creates a password reset request and sends a verification email to the user
     *
     * @param SendPasswordEmailRequest $request
     * @param SendPasswordResetEmail $sendPasswordResetEmail
     * @return void
     */
    public function store(SendPasswordEmailRequest $request, SendPasswordResetEmail $sendPasswordResetEmail)
    {
        $input = $request->validated();
        $email = $input['email'];

        $sendPasswordResetEmail->execute(
            $request->getAccount(),
            $email
        );

        return redirect()->back()->with(['success' => $email]);
    }

    /**
     * Shows the form to allow the user to set a new password
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request)
    {
        $token = $request->get('token');
        
        if ($token === null) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', 'Invalid URL. Please try again');
        }

        $passwordReset = AccountPasswordReset::where('token', $token)->first();
        if ($passwordReset === null) {
            return redirect()
                ->route('front.password-reset')
                ->withErrors('error', 'URL is invalid or has expired. Please try again');
        }

        return view('front.pages.password-reset.password-reset-form', [
            'passwordToken' => $token
        ]);
    }

    /**
     * Saves the user's new password
     *
     * @param ResetPasswordRequest $request
     * @param ResetAccountPassword $resetAccountPassword
     * @return void
     */
    public function update(ResetPasswordRequest $request, ResetAccountPassword $resetAccountPassword)
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
