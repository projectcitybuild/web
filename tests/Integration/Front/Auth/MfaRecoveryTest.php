<?php

use App\Domains\Mfa\Notifications\MfaBackupCodeUsedNotification;
use App\Http\Middleware\MfaAuthenticated;
use App\Models\Account;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class MfaRecoveryTest extends TestCase
{
    private Account $mfaAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mfaAccount = Account::factory()->hasFinishedTotp()->create();
    }

    public function test_can_view_backup_form_when2_f_aing()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->get(route('front.login.mfa-recover'))
            ->assertOk();
    }

    public function test_can_submit_backup_form()
    {
        Notification::fake();

        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->delete(route('front.login.mfa-recover'), [
            'backup_code' => Crypt::decryptString($this->mfaAccount->totp_backup_code),
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.login'))
            ->assertSessionHas('success')
            ->assertSessionMissing(MfaAuthenticated::NEEDS_MFA_KEY);

        $this->assertGuest();

        Notification::assertSentTo($this->mfaAccount, MfaBackupCodeUsedNotification::class);

        $account = $this->mfaAccount->refresh();

        $this->assertEmpty($account->totp_secret);
        $this->assertEmpty($account->totp_backup_code);
        $this->assertEmpty($account->totp_last_used);
        $this->assertFalse($account->is_totp_enabled);
    }

    public function test_wrong_backup_code_doesnt_work()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->delete(route('front.login.mfa-recover'), [
            'backup_code' => Str::random(32),
        ])->assertSessionHasErrors();
    }
}
