<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    private $g2fa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->g2fa = new Google2FA();
    }

    public function testCanViewSecurityPage()
    {
        $this->actingAs(Account::factory()->create());
        $this->get(route('front.account.security'))
            ->assertSee('Security')
            ->assertOk();
    }

    public function testStartingSetupSetsSecret()
    {
        $account = Account::factory()->create();
        $this->actingAs($account);

        $this->post(route('front.account.security.start'))
            ->assertRedirect(route('front.account.security.setup'));

        $account = $account->fresh();
        $this->assertNotNull($account->totp_secret);
        $this->assertNotNull($account->totp_backup_code);
        $this->assertFalse($account->is_totp_enabled);
    }

    public function testCanFinishSetup()
    {
        $account = Account::factory()->hasStartedTotp()->create();

        $validCode = $this->g2fa->getCurrentOtp($account->totp_secret);

        $this->actingAs($account);

        $this->post(route('front.account.security.finish'), [
            'backup_saved' => 1,
            'code' => $validCode
        ])
        ->assertRedirect(route('front.account.security'));

        $this->assertDatabaseHas('accounts', [
            'account_id' => $account->getKey(),
            'is_totp_enabled' => true,
        ]);
    }

    public function testCantReStartIfSetUp()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());
        $this->post(route('front.account.security.start'))
            ->assertForbidden();
    }

    public function testCantReSetupIfSetUp()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());
        $this->get(route('front.account.security.setup'))
            ->assertForbidden();
    }

    public function testCantReFinishIfSetUp()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());
        $this->post(route('front.account.security.finish'), [
            'backup_saved' => 1,
            'code' => '000000'
        ])->assertForbidden();
    }

    public function testAskedToConfirmPasswordToDisable()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());

        $this->get(route('front.account.security.disable'))
            ->assertRedirect(route('password.confirm'));
    }

    public function testCantDisableIfNotEnabled()
    {
        $this->actingAs(Account::factory()->hasStartedTotp()->create());
        $this->disableReauthMiddleware();
        $this->get(route('front.account.security.disable'))
            ->assertForbidden();
    }

    public function testCanSeeDisableWhenConfirmed()
    {
        $this->actingAs(Account::factory()->hasFinishedTotp()->create());
        $this->disableReauthMiddleware();
        $this->get(route('front.account.security.disable'))
            ->assertOk();
    }

    public function testCanDisableTotp()
    {
        $account = Account::factory()->hasFinishedTotp()->create();
        $this->actingAs($account);
        $this->disableReauthMiddleware();
        $this->delete(route('front.account.security.disable'))
            ->assertRedirect();

        $this->assertDatabaseHas('accounts', [
            'account_id' => $account->getKey(),
            'totp_secret' => null,
            'totp_backup_code' => null,
            'is_totp_enabled' => 0,
            'totp_last_used' => null
        ]);
    }

//    public function testAskedToRegenBackupCode()
//    {
//        $this->actingAs(Account::factory()->hasFinishedTotp()->create());
//        $this->get(route('front.account.security.regen-backup-info'))
//            ->assertRedirect(route('front.account.security.regen-backup'));
//    }
}
