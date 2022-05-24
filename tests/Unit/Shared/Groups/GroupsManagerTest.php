<?php

namespace Tests\Unit\Shared\Groups;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Shared\ExternalAccounts\Sync\Adapters\StubAccountSync;
use Shared\Groups\GroupsManager;
use Tests\TestCase;

class GroupsManagerTest extends TestCase
{
    use RefreshDatabase;

    private GroupsManager $groupsManager;
    private Group $defaultGroup;
    private Account $account;

    public function setUp(): void
    {
        parent::setUp();

        $this->defaultGroup = Group::factory()->create();
        $this->account = Account::factory()->create();

        $this->groupsManager = new GroupsManager(
            externalAccountSync:  new StubAccountSync(),
            defaultGroup: $this->defaultGroup,
        );
    }

    private function assertAccountHasGroups(array $groups): void
    {
        $this->assertEquals(
            expected: collect($groups)
                ->map(fn ($group) => $group->getKey())
                ->toArray(),
            actual: $this->account->groups
                ->map(fn ($group) => $group->getKey())
                ->toArray(),
        );
    }

    public function test_add_to_default_group()
    {
        $this->assertAccountHasGroups([]);

        $this->groupsManager->addToDefaultGroup($this->account);

        $this->assertAccountHasGroups([$this->defaultGroup]);
    }

    public function test_adds_group_to_account()
    {
        $existingGroup = Group::factory()->create();
        $this->account->groups()->attach($existingGroup);

        $this->assertAccountHasGroups([$existingGroup]);

        $newGroup = Group::factory()->create();
        $this->groupsManager->addMember(group: $newGroup, account: $this->account);

        $this->assertAccountHasGroups([$existingGroup, $newGroup]);
    }

    public function test_add_group_ignores_duplicates()
    {
        $existingGroup = Group::factory()->create();
        $this->account->groups()->attach($existingGroup);

        $this->assertAccountHasGroups([$existingGroup]);

        $this->groupsManager->addMember(group: $existingGroup, account: $this->account);

        $this->assertAccountHasGroups([$existingGroup]);
    }

    public function test_add_group_removes_default_group()
    {
        $this->account->groups()->attach($this->defaultGroup);

        $this->assertAccountHasGroups([$this->defaultGroup]);

        $newGroup = Group::factory()->create();
        $this->groupsManager->addMember(group: $newGroup, account: $this->account);

        $this->assertAccountHasGroups([$newGroup]);
    }

    public function test_add_group_can_add_default_group()
    {
        $this->assertAccountHasGroups([]);

        $this->groupsManager->addMember(group: $this->defaultGroup, account: $this->account);

        $this->assertAccountHasGroups([$this->defaultGroup]);

        $this->groupsManager->addMember(group: $this->defaultGroup, account: $this->account);

        $this->assertAccountHasGroups([$this->defaultGroup]);
    }

    public function test_removes_group_from_account()
    {
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        $this->account->groups()->attach($group1);
        $this->account->groups()->attach($group2);

        $this->assertAccountHasGroups([$group1, $group2]);

        $this->groupsManager->removeMember(group: $group2, account: $this->account);

        $this->assertAccountHasGroups([$group1]);
    }

    public function test_remove_group_adds_default_group()
    {
        $group = Group::factory()->create();
        $this->account->groups()->attach($group);

        $this->assertAccountHasGroups([$group]);

        $this->groupsManager->removeMember(group: $group, account: $this->account);

        $this->assertAccountHasGroups([$this->defaultGroup]);
    }

    public function test_remove_group_does_nothing_if_not_in_group()
    {
        $this->account->groups()->attach($this->defaultGroup);

        $this->assertAccountHasGroups([$this->defaultGroup]);

        $newGroup = Group::factory()->create();
        $this->groupsManager->removeMember(group: $newGroup, account: $this->account);

        $this->assertAccountHasGroups([$this->defaultGroup]);
    }
}
