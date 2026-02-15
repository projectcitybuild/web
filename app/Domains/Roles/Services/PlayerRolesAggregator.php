<?php

namespace App\Domains\Roles\Services;

use App\Models\Account;
use App\Models\Role;
use Illuminate\Support\Collection;

class PlayerRolesAggregator
{
    public function get(Account $account): Collection
    {
        $account->loadMissing('donationPerks.donationTier.role');

        $roles = $account?->roles ?? collect();
        if ($roles->isEmpty()) {
            $roles->add(Role::whereDefault()->first());
        }

        // Donor tiers aren't regular roles and need to be manually added in
        $donorTierRoles = optional($account, function ($account) {
            return $account->donationPerks
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->map(fn ($it) => $it->donationTier?->role)
                ->filter();
        }) ?: collect();

        foreach ($donorTierRoles as $donorTierRole) {
            $roles->add($donorTierRole);
        }

        return $roles->unique(Role::primaryKey());
    }
}
