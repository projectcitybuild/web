<?php

namespace Domain\Donations;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;

final class DonationGroupSyncService
{
    private SyncUserToDiscourse $syncAction;
    private Group $donorGroup;
    private Group $memberGroup;

    public function __construct(SyncUserToDiscourse $syncAction, Group $donorGroup, Group $memberGroup)
    {
        $this->syncAction = $syncAction;
        $this->donorGroup = $donorGroup;
        $this->memberGroup = $memberGroup;
    }

    /**
     * Adds the given account to the Donor group if they're
     * not already in it.
     */
    public function addToDonorGroup(Account $account)
    {
        $madeChange = false;

        $isInDonatorGroup = $account->groups->contains($this->donorGroup->getKey());
        if (! $isInDonatorGroup) {
            $account->groups()->attach($this->donorGroup->getKey());
            $madeChange = true;
        }

        // User should not be in both Donor and Member groups simultaneously
        $isInMemberGroup = $account->groups->contains($this->memberGroup->getKey());
        if ($isInMemberGroup) {
            $account->groups()->detach($this->memberGroup->getKey());
            $madeChange = true;
        }

        if ($madeChange) {
            $this->syncAction->setUser($account);
            $this->syncAction->syncAll();
        }
    }

    /**
     * Removes the given account from the Donor group if they're
     * currently in it.
     */
    public function removeFromDonorGroup(Account $account)
    {
        $madeChange = false;

        $isInDonatorGroup = $account->groups->contains($this->donorGroup->getKey());
        if ($isInDonatorGroup) {
            $account->groups()->detach($this->donorGroup->getKey());
//            $account->load('groups');
            $madeChange = true;
        }

        // Attach to Member group if the user has no other groups
        if ($account->groups->count() === 0) {
            $account->groups()->attach($this->memberGroup->getKey());
//            $account->load('groups');
            $madeChange = true;
        }

        if ($madeChange) {
            $this->syncAction->setUser($account);
            $this->syncAction->syncAll();
        }
    }
}
