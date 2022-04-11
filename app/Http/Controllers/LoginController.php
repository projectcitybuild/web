<?php

namespace App\Http\Controllers;

use App\Http\Middleware\MfaGate;
use App\Http\Requests\LoginRequest;
use App\Http\WebController;
use Domain\Login\UseCases\LogoutUseCase;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Library\Discourse\Api\DiscourseAdminApi;
use Library\Discourse\Exceptions\UserNotFound;
use Library\RateLimit\Storage\SessionTokenStorage;
use Library\RateLimit\TokenBucket;
use Library\RateLimit\TokenRate;

final class LoginController extends WebController
{
    public function __construct(
        private DiscourseAdminApi $discourseAdminApi,
        private LogoutUseCase $logoutUseCase,
        private AuthGuard $auth,
    ) {}

    public function create(): View
    {
        return view('v2.front.pages.login.login');
    }

    public function store(LoginRequest $request)
    {
        $refillRate = TokenRate::refill(3)->every(2, TokenRate::MINUTES);
        $sessionStorage = new SessionTokenStorage('login.rate', 5);
        $rateLimit = new TokenBucket(6, $refillRate, $sessionStorage);

        if ($rateLimit->consume(1) === false) {
            throw ValidationException::withMessages([
                'error' => ['Too many login attempts. Please try again in a few minutes'],
            ]);
        }

        if (! Auth::attempt($request->validated(), $request->filled('remember_me'))) {
            $triesLeft = floor($rateLimit->getAvailableTokens());

            throw ValidationException::withMessages([
                'error' => ['Email or password is incorrect: '.$triesLeft.' attempts remaining'],
            ]);
        }

        if (! $this->auth->user()->activated) {
            Auth::logout();

            throw ValidationException::withMessages([
                'error' => ['Your email has not been confirmed. If you didn\'t receive it, check your spam. If you need help, ask PCB staff.'],
            ]);
        }

        $account = $this->auth->user();

        $account->updateLastLogin($request->ip());

        // Set the user's nickname from Discourse if it isn't already
        if ($account->username === null) {
            try {
                $discourseUser = $this->discourseAdminApi->fetchUserByEmail($account->email);
                $account->username = $discourseUser['username'];
            } catch (UserNotFound $e) {
                $account->username = Str::random(10);
            } finally {
                $account->save();
            }
        }

        // Check if the user needs to complete 2FA
        if ($account->is_totp_enabled) {
            Session::put(MfaGate::NEEDS_MFA_KEY, 'true');
        }

        // Redirect back to the intended page
        // If the user is just logging in, perform discourse SSO
        return redirect()->intended(route('front.sso.discourse'));
    }

    /**
     * Logs out the current PCB account.
     * (called from Discourse)
     */
    public function logoutFromDiscourse(Request $request): RedirectResponse
    {
        $this->logoutUseCase->logoutOfPCB();

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

        return redirect()->route('front.home');
    }
}
