<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Group;
use Entities\Models\PanelGroupScope;
use Tests\E2ETestCase;

class PanelGroupMemberTest extends E2ETestCase
{
    public function test_group_list_only_shows_group_members()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_GROUPS,
        ]);

        $groupA = Group::factory()->create();
        $groupB = Group::factory()->create();
        $accountA = Account::factory()->create();
        $accountA->groups()->attach($groupA);
        $accountB = Account::factory()->create();
        $accountB->groups()->attach($groupB);

        $this->withoutExceptionHandling();

        $this->actingAs($admin)
            ->get(route('front.panel.groups.accounts', $groupA))
            ->assertSee($accountA->username)
            ->assertDontSee($accountB->username);
    }

    public function test_unauthorised_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $group = Group::factory()->create();
        $account = Account::factory()->create();
        $account->groups()->attach($group);

        $this->actingAs($admin)
            ->get(route('front.panel.groups.accounts', $group))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_GROUPS,
        ]);

        $group = Group::factory()->create();
        $account = Account::factory()->create();
        $account->groups()->attach($group);

        $this->actingAs($admin)
            ->get(route('front.panel.groups.accounts', $group))
            ->assertUnauthorized();
    }
}
