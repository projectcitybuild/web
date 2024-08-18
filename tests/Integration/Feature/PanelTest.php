<?php

namespace Tests\Integration\Feature;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Account;
use App\Models\Group;
use App\Models\GroupScope;
use Tests\TestCase;

class PanelTest extends TestCase
{
    private function createPanelAccessGroup(): Group
    {
        $group = Group::factory()->administrator()->create();
        $group->groupScopes()->attach(
            collect([PanelGroupScope::ACCESS_PANEL])
                ->map(fn ($case) => GroupScope::factory()->create(['scope' => $case->value]))
                ->map(fn ($model) => $model->getKey())
        );

        return $group;
    }

    public function test_guest_cannot_see_panel()
    {
        $this->get(route('front.panel.index'))
            ->assertRedirect(route('front.login'));
    }

    public function test_non_mfa_user_cannot_see_panel()
    {
        $nonMfaAccount = Account::factory()->create();
        $nonMfaAccount->groups()->attach(
            $this->createPanelAccessGroup()
        );

        $this->actingAs($nonMfaAccount)
            ->get(route('front.panel.index'))
            ->assertRedirectContains(route('front.account.security'))
            ->assertSessionHas('mfa_setup_required', true);
    }

    public function test_mfa_user_can_see_panel()
    {
        $this->withoutExceptionHandling();

        $mfaAccount = Account::factory()->hasFinishedTotp()->create();
        $mfaAccount->groups()->attach(
            $this->createPanelAccessGroup()
        );

        $this->actingAs($mfaAccount)
            ->get(route('front.panel.index'))
            ->assertOk();
    }
}
