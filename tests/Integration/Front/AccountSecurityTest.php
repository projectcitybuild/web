<?php

namespace Tests\Integration\Front;

use App\Domains\Mfa\Notifications\MfaBackupCodeRegeneratedNotification;
use App\Domains\Mfa\Notifications\MfaDisabledNotification;
use App\Domains\Mfa\Notifications\MfaEnabledNotification;
use App\Models\Account;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    private $g2fa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->g2fa = new Google2FA;
    }

    public function test_can_view_security_page()
    {
        $this->actingAs(Account::factory()->create());
        $this->get(route('front.account.settings.mfa'))
            ->assertSee('Security')
            ->assertOk();
    }

    public function test_starting_setup_sets_secret()
    {
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
    }

    public function test_secret_is_encrypted()
    {
        $account = Account::factory()->create();
        $this->actingAs($account);

        $this->post(route('front.account.settings.mfa.start'))
            ->assertRedirect(route('front.account.settings.mfa.setup'));

        $account = $account->fresh();
        $decryptedSecret = Crypt::decryptString($account->totp_secret);
        $this->assertEquals(16, strlen($decryptedSecret));
    }

    public function test_can_finish_setup()
    {
        $account = Account::factory()->hasStartedTotp()->create();

        $validCode = $this->g2fa->getCurrentOtp(Crypt::decryptString($account->totp_secret));

        $this->actingAs($account);

        $this->post(route('front.account.settings.mfa.finish'), [
            'backup_saved' => 1,
            'code' => $validCode,
        ])
            ->assertRedirect(route('front.account.settings.mfa'));

        $this->assertDatabaseHas('accounts', [
            'account_id' => $account->getKey(),
            'is_totp_enabled' => true,
        ]);
    }

    public function test_setup_sends_notification()
    {
        Notification::fake();
        $account = Account::factory()->hasStartedTotp()->create();
        $validCode = $this->g2fa->getCurrentOtp(Crypt::decryptString($account->totp_secret));
        $this->actingAs($account);
        $this->post(route('front.account.settings.mfa.finish'), [
            'backup_saved' => 1,
            'code' => $validCode,
        ]);
        Notification::assertSentTo($account, MfaEnabledNotification::class);
    }

    public function test_cant_re_start_if_set_up()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());
        $this->post(route('front.account.settings.mfa.start'))
            ->assertForbidden();
    }

    public function test_cant_re_setup_if_set_up()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());
        $this->get(route('front.account.settings.mfa.setup'))
            ->assertForbidden();
    }

    public function test_cant_re_finish_if_set_up()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());
        $this->post(route('front.account.settings.mfa.finish'), [
            'backup_saved' => 1,
            'code' => '000000',
        ])->assertForbidden();
    }

    public function test_asked_to_confirm_password_to_disable()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());

        $this->get(route('front.account.settings.mfa.disable'))
            ->assertRedirect(route('front.password.confirm'));
    }

    public function test_cant_disable_if_not_enabled()
    {
        $this->actingAs(Account::factory()->hasStartedTotp()->create());
        $this->disableReauthMiddleware();
        $this->get(route('front.account.settings.mfa.disable'))
            ->assertRedirect(route('front.account.settings.mfa'));
    }

    public function test_can_see_disable_when_confirmed()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());
        $this->disableReauthMiddleware();
        $this->get(route('front.account.settings.mfa.disable'))
            ->assertOk();
    }

    public function test_can_disable_totp()
    {
        $account = Account::factory()->hasFinishedTotp()->create();
        $this->actingAs($account);
        $this->disableReauthMiddleware();
        $this->delete(route('front.account.settings.mfa.disable'))
            ->assertRedirect();

        $this->assertDatabaseHas('accounts', [
            'account_id' => $account->getKey(),
            'totp_secret' => null,
            'totp_backup_code' => null,
            'is_totp_enabled' => 0,
            'totp_last_used' => null,
        ]);
    }

    public function test_disabling_sends_notification()
    {
        Notification::fake();
        $account = Account::factory()->hasFinishedTotp()->create();
        $this->actingAs($account);
        $this->disableReauthMiddleware();
        $this->delete(route('front.account.settings.mfa.disable'))
            ->assertRedirect();
        Notification::assertSentTo($account, MfaDisabledNotification::class);
    }

    public function test_cant_refresh_backup_if_not_enabled()
    {
        $this->actingAs(Account::factory()->hasStartedTotp()->create());
        $this->disableReauthMiddleware();
        $this->get(route('front.account.settings.mfa.reset-backup'))
            ->assertForbidden();
    }

    public function test_asked_to_confirm_pass_word_to_refresh_backup()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());

        $this->get(route('front.account.settings.mfa.reset-backup'))
            ->assertRedirect(route('front.password.confirm'));
    }

    public function test_can_see_new_backup_code_after_confirming()
    {
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
    }

    public function test_regenerating_backup_code_sends_notification()
    {
        Notification::fake();
        $account = Account::factory()->hasFinishedTotp()->create();
        $this->actingAs($account)->disableReauthMiddleware();
        $this->post(route('front.account.settings.mfa.reset-backup'));
        Notification::assertSentTo($account, MfaBackupCodeRegeneratedNotification::class);
    }
}
