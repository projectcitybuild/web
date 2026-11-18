<?php

namespace Tests\Integration\Front\Auth;

use Admin9\OidcServer\Authentication\AuthenticationRecord;
use Admin9\OidcServer\Contracts\AuthenticationRecorder;
use App\Domains\Mfa\Google2FAFake;
use App\Http\Middleware\MfaAuthenticated;
use App\Models\Account;
use Illuminate\Support\Facades\Crypt;
use Mockery;
use PragmaRX\Google2FA\Google2FA;

it('removes the MFA flag after a valid code', function () {
    $mfaAccount = Account::factory()->passwordHashed()->hasFinishedTotp()->create();
    $google2fa = $this->app->make(Google2FA::class);

    $this->actingAs($mfaAccount)
        ->flagNeedsMfa();

    $validCode = $google2fa->getCurrentOtp(Crypt::decryptString($mfaAccount->totp_secret));
    $recorder = Mockery::mock(AuthenticationRecorder::class);
    $recorder->shouldReceive('markAuthenticated')
        ->once()
        ->with('web', Mockery::on(fn (Account $account) => $account->is($mfaAccount)))
        ->andReturn(new AuthenticationRecord(
            'web', Account::class, (string) $mfaAccount->getAuthIdentifier(), now()->timestamp, str_repeat('a', 64),
        ));
    $this->app->instance(AuthenticationRecorder::class, $recorder);

    $this->post(route('front.login.mfa.submit'), ['code' => $validCode])
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

it('accepts the bypass code without decrypting the TOTP secret', function () {
    $this->setTemporaryConfig('auth.totp.bypass', true);
    $this->setTemporaryConfig('app.debug', true);
    $this->app->instance(Google2FA::class, new Google2FAFake);

    $mfaAccount = Account::factory()->passwordHashed()->hasFinishedTotp()->create();
    $mfaAccount->totp_secret = 'invalid-ciphertext';
    $mfaAccount->save();

    $this->actingAs($mfaAccount)
        ->flagNeedsMfa();

    $recorder = Mockery::mock(AuthenticationRecorder::class);
    $recorder->shouldReceive('markAuthenticated')
        ->once()
        ->with('web', Mockery::on(fn (Account $account) => $account->is($mfaAccount)))
        ->andReturn(new AuthenticationRecord(
            'web', Account::class, (string) $mfaAccount->getAuthIdentifier(), now()->timestamp, str_repeat('a', 64),
        ));
    $this->app->instance(AuthenticationRecorder::class, $recorder);

    $this->post(route('front.login.mfa.submit'), ['code' => '0000'])
        ->assertSessionMissing(MfaAuthenticated::NEEDS_MFA_KEY)
        ->assertRedirect();
});

it('rejects an incorrect MFA code', function () {
    $mfaAccount = Account::factory()->passwordHashed()->hasFinishedTotp()->create();
    $this->actingAs($mfaAccount)
        ->flagNeedsMfa();

    $this->post(route('front.login.mfa.submit'), ['code' => '000000'])
        ->assertSessionHasErrors(['code']);
});

it('requires an MFA code', function () {
    $mfaAccount = Account::factory()->passwordHashed()->hasFinishedTotp()->create();
    $this->actingAs($mfaAccount)
        ->flagNeedsMfa();

    $this->post(route('front.login.mfa.submit'), [])
        ->assertSessionHasErrors(['code']);
});

it('rejects a malformed MFA code', function () {
    $mfaAccount = Account::factory()->passwordHashed()->hasFinishedTotp()->create();
    $this->actingAs($mfaAccount)
        ->flagNeedsMfa();

    $this->post(route('front.login.mfa.submit'), ['backup_code' => 'abcdefg'])
        ->assertSessionHasErrors(['code']);
});
