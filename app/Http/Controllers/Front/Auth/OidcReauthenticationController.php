<?php

namespace App\Http\Controllers\Front\Auth;

use Admin9\OidcServer\Authentication\ReauthenticationChallenge;
use Admin9\OidcServer\Contracts\AuthenticationRecorder;
use Admin9\OidcServer\Contracts\ReauthenticationService;
use App\Domains\Login\Data\LoginCredentials;
use App\Domains\Login\Exceptions\InvalidLoginCredentialsException;
use App\Domains\Login\UseCases\LoginAccount;
use App\Http\Controllers\WebController;
use App\Http\Middleware\MfaAuthenticated;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

final class OidcReauthenticationController extends WebController
{
    public function __construct(
        private readonly ReauthenticationService $reauthentication,
        private readonly AuthenticationRecorder $authenticationRecorder,
        private readonly Google2FA $google2FA,
    ) {}

    public function show(Request $request): Response
    {
        $challenge = $this->challenge($request);

        return $this->form($request, $challenge);
    }

    public function store(Request $request, LoginAccount $loginAccount): Response|RedirectResponse
    {
        $challenge = $this->challenge($request);

        if ($request->session()->has(MfaAuthenticated::NEEDS_MFA_KEY)) {
            return $this->completeTotp($request, $challenge);
        }

        $credentials = LoginCredentials::fromArray($request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]));

        $account = Account::whereEmail($credentials->email)->first();
        if (! $account || ! Auth::guard($challenge->guard)->validate($credentials->toArray())) {
            throw ValidationException::withMessages([
                'password' => 'Email or password is incorrect.',
            ]);
        }

        if ($challenge->expectedIdentity !== null && $challenge->expectedIdentity !== [
            $challenge->guard,
            Account::class,
            (string) $account->getAuthIdentifier(),
        ]) {
            throw ValidationException::withMessages([
                'password' => 'Email or password is incorrect.',
            ]);
        }

        try {
            $account = $loginAccount->execute($credentials, false, $request->ip());
        } catch (InvalidLoginCredentialsException) {
            throw ValidationException::withMessages([
                'password' => 'Email or password is incorrect.',
            ]);
        }

        $request->session()->regenerate(true);

        if (! $account->activated) {
            $request->session()->put('url.intended', route('front.oidc.reauth', [
                'transaction' => $challenge->transactionId,
                'challenge' => $challenge->token,
            ]));

            return redirect()->route('front.activate');
        }

        if ($account->is_totp_enabled) {
            return $this->form($request, $challenge);
        }

        return $this->completeAuthentication($request, $challenge, $account);
    }

    private function completeTotp(Request $request, ReauthenticationChallenge $challenge): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $account = $request->user($challenge->guard);
        abort_unless($account instanceof Account && $account->activated && $account->is_totp_enabled, 403);

        $keyTimestamp = $this->google2FA->verifyKeyNewer(
            Crypt::decryptString($account->totp_secret),
            $request->string('code')->toString(),
            $account->totp_last_used,
        );

        if ($keyTimestamp === false) {
            throw ValidationException::withMessages([
                'code' => 'This 2FA code is not valid. Please try again.',
            ]);
        }

        $account->totp_last_used = $keyTimestamp;
        $account->save();
        Session::forget(MfaAuthenticated::NEEDS_MFA_KEY);
        $request->session()->regenerate(true);

        return $this->completeAuthentication($request, $challenge, $account);
    }

    private function completeAuthentication(
        Request $request,
        ReauthenticationChallenge $challenge,
        Account $account,
    ): RedirectResponse {
        $this->authenticationRecorder->markAuthenticated($challenge->guard, $account, null, $challenge);

        return $this->reauthentication->complete($request, $challenge);
    }

    private function challenge(Request $request): ReauthenticationChallenge
    {
        return $this->reauthentication->challenge(
            $request,
            $request->string('transaction')->toString(),
            $request->string('challenge')->toString(),
        );
    }

    private function form(Request $request, ReauthenticationChallenge $challenge): Response
    {
        return response()
            ->view('front.pages.auth.oidc-reauth', [
                'mfaPending' => $request->session()->has(MfaAuthenticated::NEEDS_MFA_KEY),
                'email' => $request->user($challenge->guard)?->email,
            ])
            ->header('Cache-Control', 'no-store')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
