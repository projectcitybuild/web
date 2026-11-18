<?php

namespace Tests\Integration\Front\Auth;

use App\Domains\Mfa\Notifications\MfaBackupCodeUsedNotification;
use App\Http\Middleware\MfaAuthenticated;
use App\Models\Account;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

it('shows the backup form when MFA is required', function () {
    $mfaAccount = Account::factory()->hasFinishedTotp()->create();
    $this->actingAs($mfaAccount)
        ->flagNeedsMfa();

    $this->get(route('front.login.mfa-recover'))
        ->assertOk();
});

it('uses a backup code to recover the account', function () {
    Notification::fake();
    $mfaAccount = Account::factory()->hasFinishedTotp()->create();

    $this->actingAs($mfaAccount)
        ->flagNeedsMfa();

    $this->delete(route('front.login.mfa-recover'), [
        'backup_code' => Crypt::decryptString($mfaAccount->totp_backup_code),
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('front.login'))
        ->assertSessionHas('success')
        ->assertSessionMissing(MfaAuthenticated::NEEDS_MFA_KEY);

    $this->assertGuest();

    Notification::assertSentTo($mfaAccount, MfaBackupCodeUsedNotification::class);

    $account = $mfaAccount->refresh();

    $this->assertEmpty($account->totp_secret);
    $this->assertEmpty($account->totp_backup_code);
    $this->assertEmpty($account->totp_last_used);
    $this->assertFalse($account->is_totp_enabled);
});

it('rejects an invalid backup code', function () {
    $mfaAccount = Account::factory()->hasFinishedTotp()->create();
    $this->actingAs($mfaAccount)
        ->flagNeedsMfa();

    $this->delete(route('front.login.mfa-recover'), [
        'backup_code' => Str::random(32),
    ])->assertSessionHasErrors();
});
