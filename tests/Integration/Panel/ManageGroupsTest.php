<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use App\Models\Group;
use Tests\TestCase;

class ManageGroupsTest extends TestCase
{
    public function test_can_view_group_list()
    {
        $group = Group::factory()->create();
        $this->actingAs($this->adminAccount())
            ->get(route('manage.groups.index'))
            ->assertSee($group->name);
    }

    public function test_forbidden_unless_admin()
    {
        $this->actingAs($this->adminAccount())
            ->get(route('manage.groups.index'))
            ->assertSuccessful();

        $this->actingAs($this->staffAccount())
            ->get(route('manage.groups.index'))
            ->assertForbidden();

        $this->actingAs(Account::factory()->create())
            ->get(route('manage.groups.index'))
            ->assertForbidden();
    }
}
