<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use Tests\TestCase;

class PanelTest extends TestCase
{
    public function testGuestCannotSeePanel()
    {
        $this->get(route('front.panel.index'))
            ->assertRedirect(route('front.login'));
    }

    public function testNonMfaUserCannotSeePanel()
    {
        $nonMfaAdmin = Account::factory()
            ->has(Group::factory()->administrator())
            ->create();

        $this->actingAs($nonMfaAdmin)
            ->get(route('front.panel.index'))
            ->assertRedirect(route('front.account.security'))
            ->assertSessionHas('mfa_setup_required', true);
    }

    public function testMfaUserCanSeePanel()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.index'))
            ->assertOk();
    }
}
