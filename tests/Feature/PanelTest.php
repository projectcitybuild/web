<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Group;
use Entities\Models\Eloquent\GroupScope;
use Entities\Models\PanelGroupScope;
use Tests\TestCase;

class PanelTest extends TestCase
{
    private function createPanelAccessGroup(): Group
    {
        $group = Group::factory()->administrator()->create();
        $group->groupScopes()->attach(
            collect([PanelGroupScope::ACCESS_PANEL])
                ->map(fn($case) => GroupScope::factory()->create(['scope' => $case->value]))
                ->map(fn($model) => $model->getKey())
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
            ->assertRedirect(route('front.account.security'))
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
