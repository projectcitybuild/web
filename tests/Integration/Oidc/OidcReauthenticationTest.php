<?php

use Admin9\OidcServer\Authentication\AuthenticationRecord;
use Admin9\OidcServer\Authentication\ReauthenticationChallenge;
use Admin9\OidcServer\Contracts\AuthenticationRecorder;
use Admin9\OidcServer\Contracts\ReauthenticationService;
use App\Http\Middleware\MfaAuthenticated;
use App\Models\Account;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

function oidcReauthenticationChallenge(): ReauthenticationChallenge
{
    return new ReauthenticationChallenge(
        transactionId: 'transaction-id',
        token: 'challenge-token',
        guard: 'web',
        expectedIdentity: null,
        issuedAt: now()->timestamp,
        expiresAt: now()->addMinutes(5)->timestamp,
    );
}

it('records a completed password-only reauthentication for its OIDC challenge', function () {
    $account = Account::factory()
        ->passwordHashed('secret')
        ->create();
    $challenge = oidcReauthenticationChallenge();

    $reauthentication = Mockery::mock(ReauthenticationService::class);
    $reauthentication->shouldReceive('challenge')->once()->andReturn($challenge);
    $reauthentication->shouldReceive('complete')->once()->andReturn(redirect('/oauth/authorize/continue'));
    $this->app->instance(ReauthenticationService::class, $reauthentication);

    $recorder = Mockery::mock(AuthenticationRecorder::class);
    $recorder->shouldReceive('markAuthenticated')
        ->once()
        ->with('web', Mockery::on(fn (Account $authenticated) => $authenticated->is($account)), null, $challenge)
        ->andReturn(new AuthenticationRecord(
            'web', Account::class, (string) $account->getAuthIdentifier(), now()->timestamp, str_repeat('a', 64),
        ));
    $this->app->instance(AuthenticationRecorder::class, $recorder);

    $this->post(route('front.oidc.reauth.submit'), [
        'transaction' => $challenge->transactionId,
        'challenge' => $challenge->token,
        'email' => $account->email,
        'password' => 'secret',
    ])->assertRedirect('/oauth/authorize/continue');
});

it('does not complete OIDC reauthentication until TOTP succeeds', function () {
    $account = Account::factory()
        ->passwordHashed('secret')
        ->hasFinishedTotp()
        ->create();
    $challenge = oidcReauthenticationChallenge();

    $reauthentication = Mockery::mock(ReauthenticationService::class);
    $reauthentication->shouldReceive('challenge')->twice()->andReturn($challenge);
    $reauthentication->shouldReceive('complete')->once()->andReturn(redirect('/oauth/authorize/continue'));
    $this->app->instance(ReauthenticationService::class, $reauthentication);

    $recorder = Mockery::mock(AuthenticationRecorder::class);
    $recorder->shouldReceive('markAuthenticated')
        ->once()
        ->with('web', Mockery::on(fn (Account $authenticated) => $authenticated->is($account)), null, $challenge)
        ->andReturn(new AuthenticationRecord(
            'web', Account::class, (string) $account->getAuthIdentifier(), now()->timestamp, str_repeat('a', 64),
        ));
    $this->app->instance(AuthenticationRecorder::class, $recorder);

    $this->post(route('front.oidc.reauth.submit'), [
        'transaction' => $challenge->transactionId,
        'challenge' => $challenge->token,
        'email' => $account->email,
        'password' => 'secret',
    ])->assertOk()
        ->assertSessionHas(MfaAuthenticated::NEEDS_MFA_KEY);

    $code = app(Google2FA::class)->getCurrentOtp(Crypt::decryptString($account->totp_secret));

    $this->post(route('front.oidc.reauth.submit'), [
        'transaction' => $challenge->transactionId,
        'challenge' => $challenge->token,
        'code' => $code,
    ])->assertRedirect('/oauth/authorize/continue')
        ->assertSessionMissing(MfaAuthenticated::NEEDS_MFA_KEY);
});
