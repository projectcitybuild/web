<?php

use Admin9\OidcServer\Authentication\AuthenticationRecord;
use Admin9\OidcServer\Contracts\AuthenticationRecorder;
use App\Models\Account;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class MfaLoginTest extends TestCase
{
    private Account $mfaAccount;
    private Google2FA $google2fa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mfaAccount = Account::factory()
            ->passwordHashed()
            ->hasFinishedTotp()
            ->create();

        $this->google2fa = $this->app->make(Google2FA::class);
    }

    public function test_submit_mfa_code_removes_flag()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $validCode = $this->google2fa->getCurrentOtp(Crypt::decryptString($this->mfaAccount->totp_secret));
        $recorder = Mockery::mock(AuthenticationRecorder::class);
        $recorder->shouldReceive('markAuthenticated')
            ->once()
            ->with('web', Mockery::on(fn (Account $account) => $account->is($this->mfaAccount)))
            ->andReturn(new AuthenticationRecord(
                'web', Account::class, (string) $this->mfaAccount->getAuthIdentifier(), now()->timestamp, str_repeat('a', 64),
            ));
        $this->app->instance(AuthenticationRecorder::class, $recorder);

        $this->post(route('front.login.mfa.submit'), ['code' => $validCode])
            ->assertSessionHasNoErrors()
            ->assertRedirect();
    }

    public function test_cant_submit_wrong_mfa_code()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->post(route('front.login.mfa.submit'), ['code' => '000000'])
            ->assertSessionHasErrors(['code']);
    }

    public function test_cant_submit_no_mfa_code()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->post(route('front.login.mfa.submit'), [])
            ->assertSessionHasErrors(['code']);
    }

    public function test_cant_submit_malformed_mfa_code()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->post(route('front.login.mfa.submit'), ['backup_code' => 'abcdefg'])
            ->assertSessionHasErrors(['code']);
    }
}
