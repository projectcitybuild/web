<?php

namespace App\Core\Domains\Groups;

use App\Models\Account;
use App\Models\Group;

/**
 * @final
 */
class GroupsManager
{
    public function __construct(
        private Group $defaultGroup,  // a.k.a. the Member group
    ) {
    }

    /**
     * Adds the given account to the Member group if they have
     * no other group currently attached
     */
    public function addToDefaultGroup(Account $account): void
    {
        $this->addMember(group: $this->defaultGroup, account: $account);
    }

    public function addMember(Group $group, Account $account): void
    {
        $isInTargetGroup = $account->groups->contains($group->getKey());
        if (! $isInTargetGroup) {
            $account->groups()->attach($group->getKey());
            $account->load('groups');
        }

        // Remove Default group if they're in any other group
        $isInDefaultGroup = $account->groups->contains($this->defaultGroup->getKey());
        $nonDefaultGroupCount = $account->groups
            ->filter(fn ($g) => $g->getKey() !== $this->defaultGroup->getKey())
            ->count();

        if ($isInDefaultGroup && $nonDefaultGroupCount > 0) {
            $account->groups()->detach($this->defaultGroup->getKey());
            $account->load('groups');
        }
    }

    public function removeMember(Group $group, Account $account): void
    {
        $isInTargetGroup = $account->groups->contains($group->getKey());
        if ($isInTargetGroup) {
            $account->groups()->detach($group->getKey());
            $account->load('groups');
        }

        // Add to Default group if the account has no other groups
        if ($account->groups->count() === 0) {
            $account->groups()->attach($this->defaultGroup->getKey());
            $account->load('groups');
        }
    }
}
