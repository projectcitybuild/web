<?php

namespace Tests\Integration\Front;

use App\Domains\Mfa\Notifications\MfaBackupCodeRegeneratedNotification;
use App\Domains\Mfa\Notifications\MfaDisabledNotification;
use App\Domains\Mfa\Notifications\MfaEnabledNotification;
use App\Models\Account;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use PragmaRX\Google2FA\Google2FA;

it('shows the security page', function () {
    $this->actingAs(Account::factory()->create());
    $this->get(route('front.account.settings.mfa'))
        ->assertSee('Security')
        ->assertOk();
});

it('creates TOTP secrets when setup starts', function () {
    $account = Account::factory()->create();
    $this->actingAs($account);

    $resp = $this->followingRedirects()
        ->post(route('front.account.settings.mfa.start'))
        ->assertOk();

    $account = $account->fresh();
    $this->assertNotNull($account->totp_secret);
    $this->assertNotNull($account->totp_backup_code);
    $this->assertFalse($account->is_totp_enabled);

    $resp->assertSee(Crypt::decryptString($account->totp_backup_code))
        ->assertSee(Crypt::decryptString($account->totp_secret));
});

it('stores TOTP secrets encrypted', function () {
    $account = Account::factory()->create();
    $this->actingAs($account);

    $this->post(route('front.account.settings.mfa.start'))
        ->assertRedirect(route('front.account.settings.mfa.setup'));

    $account = $account->fresh();
    $decryptedSecret = Crypt::decryptString($account->totp_secret);
    $this->assertEquals(32, strlen($decryptedSecret));
});

it('enables TOTP with a valid code', function () {
    $account = Account::factory()->hasStartedTotp()->create();

    $validCode = (new Google2FA)->getCurrentOtp(Crypt::decryptString($account->totp_secret));

    $this->actingAs($account);

    $this->post(route('front.account.settings.mfa.finish'), [
        'backup_saved' => 1,
        'code' => $validCode,
    ])
        ->assertRedirect(route('front.account.settings.mfa'));

    $this->assertDatabaseHas(Account::tableName(), [
        'id' => $account->id,
        'is_totp_enabled' => true,
    ]);
});

it('sends a notification when TOTP setup completes', function () {
    Notification::fake();
    $account = Account::factory()->hasStartedTotp()->create();
    $validCode = (new Google2FA)->getCurrentOtp(Crypt::decryptString($account->totp_secret));
    $this->actingAs($account);
    $this->post(route('front.account.settings.mfa.finish'), [
        'backup_saved' => 1,
        'code' => $validCode,
    ]);
    Notification::assertSentTo($account, MfaEnabledNotification::class);
});

it('forbids starting TOTP setup when already enabled', function () {
    $this->actingAs(Account::factory()->hasFinishedTotp()->create());
    $this->post(route('front.account.settings.mfa.start'))
        ->assertForbidden();
});

it('forbids viewing setup when TOTP is already enabled', function () {
    $this->actingAs(Account::factory()->hasFinishedTotp()->create());
    $this->get(route('front.account.settings.mfa.setup'))
        ->assertForbidden();
});

it('forbids completing setup when TOTP is already enabled', function () {
    $this->actingAs(Account::factory()->hasFinishedTotp()->create());
    $this->post(route('front.account.settings.mfa.finish'), [
        'backup_saved' => 1,
        'code' => '000000',
    ])->assertForbidden();
});

it('requires password confirmation before disabling TOTP', function () {
    $this->actingAs(Account::factory()->hasFinishedTotp()->create());

    $this->get(route('front.account.settings.mfa.disable'))
        ->assertRedirect(route('front.password.confirm'));
});

it('redirects when disabling TOTP that is not enabled', function () {
    $this->actingAs(Account::factory()->hasStartedTotp()->create());
    $this->disableReauthMiddleware();
    $this->get(route('front.account.settings.mfa.disable'))
        ->assertRedirect(route('front.account.settings.mfa'));
});

it('shows the disable page after password confirmation', function () {
    $this->actingAs(Account::factory()->hasFinishedTotp()->create());
    $this->disableReauthMiddleware();
    $this->get(route('front.account.settings.mfa.disable'))
        ->assertOk();
});

it('clears TOTP data when TOTP is disabled', function () {
    $account = Account::factory()->hasFinishedTotp()->create();
    $this->actingAs($account);
    $this->disableReauthMiddleware();
    $this->delete(route('front.account.settings.mfa.disable'))
        ->assertRedirect();

    $this->assertDatabaseHas(Account::tableName(), [
        'id' => $account->id,
        'totp_secret' => null,
        'totp_backup_code' => null,
        'is_totp_enabled' => 0,
        'totp_last_used' => null,
    ]);
});

it('sends a notification when TOTP is disabled', function () {
    Notification::fake();
    $account = Account::factory()->hasFinishedTotp()->create();
    $this->actingAs($account);
    $this->disableReauthMiddleware();
    $this->delete(route('front.account.settings.mfa.disable'))
        ->assertRedirect();
    Notification::assertSentTo($account, MfaDisabledNotification::class);
});

it('forbids refreshing a backup code when TOTP is not enabled', function () {
    $this->actingAs(Account::factory()->hasStartedTotp()->create());
    $this->disableReauthMiddleware();
    $this->get(route('front.account.settings.mfa.reset-backup'))
        ->assertForbidden();
});

it('requires password confirmation before refreshing a backup code', function () {
    $this->actingAs(Account::factory()->hasFinishedTotp()->create());

    $this->get(route('front.account.settings.mfa.reset-backup'))
        ->assertRedirect(route('front.password.confirm'));
});

it('shows the new backup code after confirmation', function () {
    $account = Account::factory()->hasFinishedTotp()->create();
    $originalCode = $account->totp_backup_code;

    $this->withoutExceptionHandling();
    $this->actingAs($account);
    $this->disableReauthMiddleware();

    $resp = $this->post(route('front.account.settings.mfa.reset-backup'))
        ->assertOk();

    $account = $account->fresh();
    $newCode = $account->totp_backup_code;
    $this->assertNotEquals($originalCode, $newCode);

    $resp->assertSee(Crypt::decryptString($newCode));
});

it('notifies the account when its backup code is regenerated', function () {
    Notification::fake();
    $account = Account::factory()->hasFinishedTotp()->create();
    $this->actingAs($account)->disableReauthMiddleware();
    $this->post(route('front.account.settings.mfa.reset-backup'));
    Notification::assertSentTo($account, MfaBackupCodeRegeneratedNotification::class);
});
