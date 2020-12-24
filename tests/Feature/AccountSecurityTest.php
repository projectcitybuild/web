<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    public function testCanViewSecurityPage()
    {
        $this->actingAs(Account::factory()->create());
        $this->get(route('front.account.security'))
            ->assertSee('Security')
            ->assertOk();
    }

    public function testCanRetrieveCode()
    {
        $this->actingAs(Account::factory()->create());
        $this->post(route('front.account.security.enable'))
            ->assertOk()
            ->assertSee('qr');
    }
}
