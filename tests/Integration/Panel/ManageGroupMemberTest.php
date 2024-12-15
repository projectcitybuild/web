<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use App\Models\Group;
use Tests\TestCase;

class ManageGroupMemberTest extends TestCase
{
    public function test_group_list_only_shows_group_members()
    {
        $groupA = Group::factory()->create();
        $groupB = Group::factory()->create();
        $accountA = Account::factory()->create();
        $accountA->groups()->attach($groupA);
        $accountB = Account::factory()->create();
        $accountB->groups()->attach($groupB);

        $this->withoutExceptionHandling();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.groups.accounts', $groupA))
            ->assertSee($accountA->username)
            ->assertDontSee($accountB->username);
    }

    public function test_forbidden_unless_admin()
    {
        $group = Group::factory()->create();
        $account = Account::factory()->create();
        $account->groups()->attach($group);

        $this->actingAs($this->adminAccount())
            ->get(route('manage.groups.accounts', $group))
            ->assertSuccessful();

        $this->actingAs($this->staffAccount())
            ->get(route('manage.groups.accounts', $group))
            ->assertForbidden();

        $this->actingAs(Account::factory()->create())
            ->get(route('manage.groups.accounts', $group))
            ->assertForbidden();
    }
}
