<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Http\Middleware\MfaGate;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
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

    private function flagNeedsMfa()
    {
        Session::put(MfaGate::NEEDS_MFA_KEY, "true");

    }

    public function testMfaFlagIsSetOnLogin()
    {
        $this->post(route('front.login.submit'), [
            'email' => $this->mfaAccount->email,
            'password' => 'secret'
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

        $this->withoutExceptionHandling();

        $validCode = $this->google2fa->getCurrentOtp(Crypt::decryptString($this->mfaAccount->totp_secret));

        $this->post(route('front.login.mfa'), [
            'code' => $validCode
        ])->assertSessionHasNoErrors()->assertRedirect();
    }
}
