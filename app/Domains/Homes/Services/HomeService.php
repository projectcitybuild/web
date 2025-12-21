<?php

namespace App\Domains\Homes\Services;

use App\Domains\Groups\Services\PlayerGroupsAggregator;
use App\Domains\Homes\Data\HomeCount;
use App\Models\Group;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;

class HomeService
{
    public function __construct(
        // TODO: inject with interface to break coupling
        private readonly PlayerGroupsAggregator $playerGroupsAggregator,
    ) {}

    public function count(MinecraftPlayer $player): ?HomeCount
    {
        $account = $player->account;
        $groups = optional($account, fn ($it) => $this->playerGroupsAggregator->get($it))
            ?? collect();

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
