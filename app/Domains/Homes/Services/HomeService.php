<?php

namespace App\Domains\Homes\Services;

use App\Domains\Homes\Data\HomeCount;
use App\Models\Group;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;

class HomeService
{
    public function count(MinecraftPlayer $player): ?HomeCount
    {
        $account = $player->account;
        $account?->load('donationPerks.donationTier.group');

        $groups = $account?->groups ?? collect();
        if ($groups->isEmpty()) {
            $groups->add(Group::whereDefault()->first());
        }

        // Donor tiers aren't regular groups and need to be manually added in
        $donorTierGroups = $account?->donationPerks
            ?->pluck('donationTier.group')
            ?->filter()
            ?->unique('group_id')
            ?->values() ?? collect();

        foreach ($donorTierGroups as $donorGroup) {
            $groups->add($donorGroup);
        }

        $sources = $groups
            ->filter(fn ($group) => ($group->additional_homes ?? 0) > 0)
            ->pluck('additional_homes', 'name');

        return new HomeCount(
            used: MinecraftHome::where('player_id', $player->getKey())->count(),
            allowed: max(1, $sources->sum()), // Always grant at least 1 home
            sources: $sources->toArray(),
        );
    }
}
