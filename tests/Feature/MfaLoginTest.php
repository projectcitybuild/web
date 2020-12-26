<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use Tests\TestCase;

class MfaLoginTest extends TestCase
{
    private $mfaAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mfaAccount = Account::factory()->hasFinishedTotp()->create();
    }

    public function testMfaAccountIsRedirectedToChallengeOnLogin()
    {
        $this->actingAs($this->mfaAccount);
        $this->withoutExceptionHandling();
        $this->post(route('front.login'), [
            'email' => $this->mfaAccount->email,
            'password' => 'secret'
        ])->assertSessionHas('auth.needs-mfa');
    }
}
