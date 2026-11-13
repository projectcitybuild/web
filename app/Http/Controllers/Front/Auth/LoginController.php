<?php

namespace App\Http\Controllers\Front\Auth;

use Admin9\OidcServer\Contracts\AuthenticationRecorder;
use App\Domains\Login\Data\LoginCredentials;
use App\Domains\Login\Exceptions\InvalidLoginCredentialsException;
use App\Domains\Login\UseCases\LoginAccount;
use App\Http\Controllers\WebController;
use App\Http\Middleware\MfaAuthenticated;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

final class LoginController extends WebController
{
    public function show(): View
    {
        return view('front.pages.auth.login');
    }

    public function login(
        LoginRequest $request,
        LoginAccount $loginAccount,
        AuthenticationRecorder $authenticationRecorder,
    ): RedirectResponse {
        $validated = $request->validated();

        $credentials = LoginCredentials::fromArray($validated);

        try {
            $account = $loginAccount->execute(
                credentials: $credentials,
                shouldRemember: $request->filled('remember_me'),
                ip: $request->ip(),
            );
        } catch (InvalidLoginCredentialsException) {
            throw ValidationException::withMessages([
                'error' => 'Email or password is incorrect',
            ]);
        }

        // Prevent session fixation
        // https://laravel.com/docs/9.x/authentication#authenticating-users
        $request->session()->regenerate(true);

        if (! $request->session()->has(MfaAuthenticated::NEEDS_MFA_KEY)) {
            $authenticationRecorder->markAuthenticated('web', $account);
        }

        return redirect()->intended(route('front.account.profile'));
    }
}
