<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Group;
use Tests\TestCase;

class PanelTest extends TestCase
{
    public function test_guest_cannot_see_panel()
    {
        $this->get(route('front.panel.index'))
            ->assertRedirect(route('front.login'));
    }

    public function test_non_mfa_user_cannot_see_panel()
    {
        $nonMfaAdmin = Account::factory()
            ->has(Group::factory()->administrator())
            ->create();

        $this->actingAs($nonMfaAdmin)
            ->get(route('front.panel.index'))
            ->assertRedirect(route('front.account.security'))
            ->assertSessionHas('mfa_setup_required', true);
    }

    public function test_mfa_user_can_see_panel()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.index'))
            ->assertOk();
    }
}
