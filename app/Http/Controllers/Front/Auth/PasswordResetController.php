<?php

namespace App\Http\Controllers\Front\Auth;

use App\Domains\PasswordReset\UseCases\ResetAccountPassword;
use App\Domains\PasswordReset\UseCases\SendPasswordResetEmail;
use App\Http\Controllers\WebController;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendPasswordEmailRequest;
use App\Models\PasswordReset;
use Illuminate\Http\Request;

final class PasswordResetController extends WebController
{
    public function create()
    {
        return view('front.pages.auth.password-reset.form');
    }

    public function store(
        SendPasswordEmailRequest $request,
        SendPasswordResetEmail $sendPasswordResetEmail,
    ) {
        $input = $request->validated();
        $email = $input['email'];

        $sendPasswordResetEmail->execute(
            account: $request->getAccount(),
            email: $email,
        );

        return redirect()
            ->back()
            ->with(['success' => 'An email has been sent to '.$email.' with password reset instructions.']);
    }

    public function edit(Request $request)
    {
        $token = $request->get('token');

        if ($token === null) {
            return redirect()
                ->route('front.password-reset.create')
                ->withErrors('error', 'Invalid URL. Please try again');
        }

        $passwordReset = PasswordReset::where('token', $token)->first();
        if ($passwordReset === null) {
            return redirect()
                ->route('front.password-reset.create')
                ->withErrors('error', 'URL is invalid or has expired. Please try again');
        }

        return view('front.pages.auth.password-reset.set-password', [
            'passwordToken' => $token,
        ]);
    }

    public function update(
        ResetPasswordRequest $request,
        ResetAccountPassword $resetAccountPassword,
    ) {
        $input = $request->validated();

        $resetAccountPassword->execute(
            token: $input['password_token'],
            newPassword: $input['password'],
        );

        return redirect()
            ->route('front.login')
            ->with('success', 'Your password has been reset. Please login');
    }
}
