<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Http\Middleware\MfaGate;
use Illuminate\Support\Str;
use Tests\TestCase;

class MfaBackupTest extends TestCase
{
    private Account $mfaAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mfaAccount = Account::factory()->hasFinishedTotp()->create();
    }

    public function testCanViewBackupFormWhen2FAing()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->get(route('front.login.mfa-recover'))
            ->assertOk();
    }

    public function testCanSubmitBackupForm()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->delete(route('front.login.mfa-recover'), [
            'backup_code' => $this->mfaAccount->totp_backup_code
        ])
            ->assertRedirect(route('front.login'))
            ->assertSessionHas('mfa_removed')
            ->assertSessionMissing(MfaGate::NEEDS_MFA_KEY);

        $account = $this->mfaAccount->refresh();

        $this->assertEmpty($account->totp_secret);
        $this->assertEmpty($account->totp_backup_code);
        $this->assertEmpty($account->totp_last_used);
        $this->assertFalse($account->is_totp_enabled);
    }

    public function testWrongBackupCodeDoesntWork()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->delete(route('front.login.mfa-recover'), [
            'backup_code' => Str::random(32)
        ])->assertSessionHasErrors();
    }
}
