<?php

namespace Shared\Groups;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\Group;
use Shared\ExternalAccounts\ExternalAccountSync;

/**
 * @final
 */
class GroupsManager
{
    public function __construct(
        private ExternalAccountSync $externalAccountSync,
        private Group $defaultGroup,  // a.k.a. the Member group
    ) {}

    public function addMember(Group $group, Account $account): void
    {
        $isInTargetGroup = $account->groups->contains($group->getKey());
        if ($isInTargetGroup) {
            return;
        }

        $account->groups()->attach($group->getKey());
        $account->load('groups');

        // Remove Default group if they're currently in it
        $isInDefaultGroup = $account->groups->contains($this->defaultGroup->getKey());
        if ($isInDefaultGroup) {
            $account->groups()->detach($this->defaultGroup->getKey());
            $account->load('groups');
        }

        $this->externalAccountSync->sync($account);
    }

    public function removeMember(Group $group, Account $account): void
    {
        $isInTargetGroup = $account->groups->contains($group->getKey());
        if (! $isInTargetGroup) {
            return;
        }

        $account->groups()->detach($group->getKey());
        $account->load('groups');

        // Add to Default group if the account has no other groups
        if ($account->groups->count() === 0) {
            $account->groups()->attach($this->defaultGroup->getKey());
            $account->load('groups');
        }

        $this->externalAccountSync->sync($account);
    }
}
