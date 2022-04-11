<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\WebController;
use Domain\Login\Entities\LoginCredentials;
use Domain\Login\Exceptions\AccountNotActivatedException;
use Domain\Login\Exceptions\InvalidLoginCredentialsException;
use Domain\Login\UseCases\LoginUseCase;
use Domain\Login\UseCases\LogoutUseCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Library\RateLimit\Storage\SessionTokenStorage;
use Library\RateLimit\TokenBucket;
use Library\RateLimit\TokenRate;

final class LoginController extends WebController
{
    public function __construct(
        private LogoutUseCase $logoutUseCase,
    ) {}

    public function create(): View
    {
        return view('v2.front.pages.login.login');
    }

    public function store(LoginRequest $request, LoginUseCase $loginUseCase)
    {
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
            $loginUseCase->execute(
                credentials: $credentials,
                shouldRemember: $request->filled('remember_me'),
                ip: $request->ip(),
            );
        }
        catch (InvalidLoginCredentialsException) {
            $triesLeft = floor($rateLimit->getAvailableTokens());
            throw ValidationException::withMessages([
                'error' => ['Email or password is incorrect: ' . $triesLeft . ' attempts remaining'],
            ]);
        }
        catch (AccountNotActivatedException) {
            throw ValidationException::withMessages([
                'error' => ['Your email has not been confirmed. If you didn\'t receive it, check your spam. If you need help, ask PCB staff.'],
            ]);
        }

        // Prevent session fixation
        // https://laravel.com/docs/9.x/authentication#authenticating-users
        $request->session()->regenerate();

        // SSO login needs to be in separate route due to reuse by Discourse
        return redirect()->intended(route('front.sso.discourse'));
    }

    /**
     * Logs out the current PCB account.
     * (called from Discourse)
     */
    public function logoutFromDiscourse(Request $request): RedirectResponse
    {
        $this->logoutUseCase->logoutOfPCB();

        // Prevent session fixation
        // https://laravel.com/docs/9.x/authentication#logging-out
        $request->session()->regenerateToken();

        return redirect()->route('front.home');
    }

    /**
     * Logs out the current PCB account and its associated Discourse account.
     *
     * (called from this site)
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->logoutUseCase->logoutOfDiscourseAndPCB();

        // Prevent session fixation
        // https://laravel.com/docs/9.x/authentication#logging-out
        $request->session()->regenerateToken();

        return redirect()->route('front.home');
    }
}
