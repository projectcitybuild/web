<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use App\Models\Group;
use Tests\TestCase;

class ManageTest extends TestCase
{
    public function test_guest_cannot_see_panel()
    {
        $this->get(route('manage.index'))
            ->assertRedirect(route('front.login'));
    }

    public function test_non_mfa_user_cannot_see_panel()
    {
        $nonMfaAccount = Account::factory()->create();
        $nonMfaAccount->groups()->attach(Group::factory()->staff()->create());

        $this->actingAs($nonMfaAccount)
            ->get(route('manage.index'))
            ->assertRedirectContains(route('front.account.settings.mfa'))
            ->assertSessionHas('mfa_setup_required', true);
    }

    public function test_mfa_user_can_see_panel()
    {
        $this->withoutExceptionHandling();

        $mfaAccount = Account::factory()->hasFinishedTotp()->create();
        $mfaAccount->groups()->attach(Group::factory()->staff()->create());

        $this->actingAs($mfaAccount)
            ->get(route('manage.index'))
            ->assertOk();
    }
}
