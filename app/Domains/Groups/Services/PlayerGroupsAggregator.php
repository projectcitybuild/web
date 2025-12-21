<?php

namespace App\Domains\Groups\Services;

use App\Models\Account;
use App\Models\Group;
use Illuminate\Support\Collection;

class PlayerGroupsAggregator
{
    public function get(Account $account): Collection
    {
        $account->loadMissing('donationPerks.donationTier.group');

        $groups = $account?->groups ?? collect();
        if ($groups->isEmpty()) {
            $groups->add(Group::whereDefault()->first());
        }

        // Donor tiers aren't regular groups and need to be manually added in
        $donorTierGroups = optional($account, function ($account) {
            return $account->donationPerks
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->map(fn ($it) => $it->donationTier?->group)
                ->filter();
        }) ?: collect();

        foreach ($donorTierGroups as $donorTierGroup) {
            $groups->add($donorTierGroup);
        }

        return $groups->unique('group_id');
    }
}
