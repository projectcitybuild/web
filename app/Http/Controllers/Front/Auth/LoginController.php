<?php

namespace App\Http\Controllers\Front\Auth;

use App\Domains\Login\Data\LoginCredentials;
use App\Domains\Login\Exceptions\InvalidLoginCredentialsException;
use App\Domains\Login\UseCases\LoginAccount;
use App\Http\Controllers\WebController;
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
    ): RedirectResponse {
        $validated = $request->validated();

        $credentials = LoginCredentials::fromArray($validated);

        try {
            $loginAccount->execute(
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
        $request->session()->regenerate();

        return redirect()->intended(route('front.account.profile'));
    }
}
