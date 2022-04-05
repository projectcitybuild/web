<?php

namespace App\Http\Controllers;

use App\Exceptions\Http\NotFoundException;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordEmailRequest;
use App\Http\WebController;
use Domain\PasswordReset\Repositories\AccountPasswordResetRepository;
use Domain\PasswordReset\UseCases\ResetAccountPasswordUseCase;
use Domain\PasswordReset\UseCases\SendPasswordResetEmailUseCase;
use Illuminate\Http\Request;

final class PasswordResetController extends WebController
{
    /**
     * Shows the form to send a verification URL to the user's email address.
     */
    public function create()
    {
        return view('front.pages.password-reset.password-reset');
    }

    /**
     * Creates a password reset request and sends a verification email to the user.
     */
    public function store(SendPasswordEmailRequest $request, SendPasswordResetEmailUseCase $sendPasswordResetEmail)
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
     * Shows the form to allow the user to set a new password.
     */
    public function edit(
        Request $request,
        AccountPasswordResetRepository $passwordResetRepository,
    ) {
        $token = $request->get('token');

        if ($token === null) {
            return redirect()
                ->route('front.password-reset.create')
                ->withErrors('error', 'Invalid URL. Please try again');
        }

        $passwordReset = $passwordResetRepository->firstByToken($token);
        if ($passwordReset === null) {
            return redirect()
                ->route('front.password-reset.create')
                ->withErrors('error', 'URL is invalid or has expired. Please try again');
        }

        return view('front.pages.password-reset.password-reset-form', [
            'passwordToken' => $token,
        ]);
    }

    /**
     * Saves the user's new password.
     */
    public function update(ResetPasswordRequest $request, ResetAccountPasswordUseCase $resetAccountPassword)
    {
        $input = $request->validated();

        try {
            $resetAccountPassword->execute(
                token: $input['password_token'],
                newPassword: $input['password']
            );
        } catch (NotFoundException $e) {
            return redirect()
                ->route('front.password-reset.create')
                ->withErrors('error', $e->getMessage());
        }

        return view('front.pages.password-reset.password-reset-success');
    }
}
