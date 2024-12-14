<?php

namespace Panel;

use App\Domains\Manage\Data\PanelGroupScope;
use App\Models\Group;
use Tests\IntegrationTestCase;

class PanelGroupsTest extends IntegrationTestCase
{
    public function test_can_view_group_list()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::MANAGE_GROUPS,
        ]);

        $group = Group::factory()->create();
        $this->actingAs($admin)
            ->get(route('manage.groups.index'))
            ->assertSee($group->name);
    }

    public function test_unauthorised_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->get(route('manage.groups.index'))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_GROUPS,
        ]);

        $this->actingAs($admin)
            ->get(route('manage.groups.index'))
            ->assertUnauthorized();
    }
}
