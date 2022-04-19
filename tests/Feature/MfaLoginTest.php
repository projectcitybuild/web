<?php

namespace Tests\Feature;

use App\Http\Middleware\MfaGate;
use Entities\Models\Eloquent\Account;
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

        $this->mfaAccount = Account::factory()->hasFinishedTotp()->create();
        $this->google2fa = $this->app->make(Google2FA::class);
    }

    public function testMfaFlagIsSetOnLogin()
    {
        $this->post(route('front.login.submit'), [
            'email' => $this->mfaAccount->email,
            'password' => 'secret',
        ])->assertSessionHas(MfaGate::NEEDS_MFA_KEY);
    }

    public function testMfaFlagCausesRedirect()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->get(route('front.account.settings'))
            ->assertRedirect(route('front.login.mfa'));
    }

    public function testSubmitMfaCodeRemovesFlag()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $validCode = $this->google2fa->getCurrentOtp(Crypt::decryptString($this->mfaAccount->totp_secret));

        $this->post(route('front.login.mfa'), [
            'code' => $validCode,
        ])->assertSessionHasNoErrors()->assertRedirect();
    }

    public function testCantSubmitWrongMfaCode()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->post(route('front.login.mfa'), [
            'code' => '000000',
        ])->assertSessionHasErrors(['code']);
    }

    public function testCantSubmitNoMfaCode()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->post(route('front.login.mfa'), [])
            ->assertSessionHasErrors(['code']);
    }

    public function testCantSubmitMalformedMfaCode()
    {
        $this->actingAs($this->mfaAccount)
            ->flagNeedsMfa();

        $this->post(route('front.login.mfa'), ['backup_code' => 'abcdefg'])
            ->assertSessionHasErrors(['code']);
    }
}
