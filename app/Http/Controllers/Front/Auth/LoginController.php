<?php

namespace App\Http\Controllers\Front\Auth;

use App\Domains\Login\Entities\LoginCredentials;
use App\Domains\Login\Exceptions\AccountNotActivatedException;
use App\Domains\Login\Exceptions\InvalidLoginCredentialsException;
use App\Domains\Login\UseCases\LoginAccount;
use App\Domains\Registration\Exceptions\AccountAlreadyActivatedException;
use App\Domains\Registration\UseCases\ResendActivationEmail;
use App\Http\Controllers\WebController;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $credentials = new LoginCredentials(
            email: $validated['email'],
            password: $validated['password'],
        );

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
        } catch (AccountNotActivatedException) {
            return redirect()->route('front.activate', [
                'email' => $validated['email'],
            ]);
        }

        // Prevent session fixation
        // https://laravel.com/docs/9.x/authentication#authenticating-users
        $request->session()->regenerate();

        return redirect()->intended();
    }
}
