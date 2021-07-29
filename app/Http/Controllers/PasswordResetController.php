<?php

namespace App\Http\Controllers;

use App\Entities\Accounts\Models\AccountPasswordReset;
use App\Exceptions\Http\NotFoundException;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordEmailRequest;
use App\Http\WebController;
use Domain\PasswordReset\PasswordResetService;
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
    public function store(SendPasswordEmailRequest $request, PasswordResetService $passwordResetService)
    {
        $input = $request->validated();
        $email = $input['email'];

        $passwordResetService->sendPasswordResetEmail(
            $request->getAccount(),
            $email
        );

        return redirect()->back()->with(['success' => $email]);
    }

    /**
     * Shows the form to allow the user to set a new password.
     */
    public function edit(Request $request)
    {
        $token = $request->get('token');

        if ($token === null) {
            return redirect()
                ->route('front.password-reset.create')
                ->withErrors('error', 'Invalid URL. Please try again');
        }

        $passwordReset = AccountPasswordReset::where('token', $token)->first();
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
    public function update(ResetPasswordRequest $request, PasswordResetService $passwordResetService)
    {
        $input = $request->validated();

        try {
            $passwordResetService->resetPassword(
                $input['password_token'],
                $input['password']
            );
        } catch (NotFoundException $e) {
            return redirect()
                ->route('front.password-reset.create')
                ->withErrors('error', $e->getMessage());
        }

        return view('front.pages.password-reset.password-reset-success');
    }
}
