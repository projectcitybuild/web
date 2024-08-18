<?php

namespace App\Http\Controllers\Front;

use App\Core\Domains\RateLimit\Storage\SessionTokenStorage;
use App\Core\Domains\RateLimit\TokenBucket;
use App\Core\Domains\RateLimit\TokenRate;
use App\Domains\Login\Entities\LoginCredentials;
use App\Domains\Login\Exceptions\AccountNotActivatedException;
use App\Domains\Login\Exceptions\InvalidLoginCredentialsException;
use App\Domains\Login\UseCases\LoginAccount;
use App\Domains\SignUp\Exceptions\AccountAlreadyActivatedException;
use App\Domains\SignUp\UseCases\ResendActivationEmail;
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
        return view('front.pages.login.login');
    }

    public function login(
        LoginRequest $request,
        LoginAccount $loginAccount,
    ): RedirectResponse {
        $input = $request->validated();

        $rateLimit = new TokenBucket(
            capacity: 6,
            refillRate: TokenRate::refill(3)
                ->every(2, TokenRate::MINUTES),
            storage: new SessionTokenStorage(
                sessionName: 'login.rate',
                initialTokens: 5
            ),
        );

        if (! $rateLimit->consume(1)) {
            throw ValidationException::withMessages([
                'error' => ['Too many login attempts. Please try again in a few minutes'],
            ]);
        }

        $credentials = new LoginCredentials(
            email: $input['email'],
            password: $input['password'],
        );

        try {
            $loginAccount->execute(
                credentials: $credentials,
                shouldRemember: $request->filled('remember_me'),
                ip: $request->ip(),
            );
        } catch (InvalidLoginCredentialsException) {
            $triesLeft = floor($rateLimit->getAvailableTokens());
            throw ValidationException::withMessages([
                'error' => ['Email or password is incorrect: '.$triesLeft.' attempts remaining'],
            ]);
        } catch (AccountNotActivatedException) {
            throw ValidationException::withMessages([
                'error' => ['Your email has not been confirmed. If you didn\'t receive it, check your spam. <p /><a href="'.route('front.login.reactivate', ['email' => $input['email']]).'">Click here</a> if you need to resend the activation email'],
            ]);
        }

        // Prevent session fixation
        // https://laravel.com/docs/9.x/authentication#authenticating-users
        $request->session()->regenerate();

        return redirect()->intended();
    }

    public function resendActivationEmail(
        Request $request,
        ResendActivationEmail $resendActivationEmail,
    ) {
        try {
            $resendActivationEmail->execute(email: $request->get('email'));
        } catch (AccountAlreadyActivatedException) {
            return redirect()->back()->withErrors([
                'Account has already been activated',
            ]);
        }

        return redirect()->back()->with(
            key: 'success',
            value: 'An activation email has been sent if the account exists. Please remember to check your spam/junk',
        );
    }
}
