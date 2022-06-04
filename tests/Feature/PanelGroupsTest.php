<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Group;
use Entities\Models\PanelGroupScope;
use Tests\E2ETestCase;

class PanelGroupsTest extends E2ETestCase
{
    public function test_can_view_group_list()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_GROUPS,
        ]);

        $group = Group::factory()->create();
        $this->actingAs($admin)
            ->get(route('front.panel.groups.index'))
            ->assertSee($group->name);
    }

    public function test_forbidden_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.groups.index'))
            ->assertForbidden();
    }

    public function test_forbidden_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_GROUPS,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.groups.index'))
            ->assertForbidden();
    }
}
