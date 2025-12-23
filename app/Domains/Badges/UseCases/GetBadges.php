<?php

namespace App\Domains\Badges\UseCases;

use App\Models\Badge;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Collection;

final class GetBadges
{
    public function execute(MinecraftPlayer $player): Collection
    {
        $account = $player->account;
        if ($account === null) {
            return collect();
        }

        $badges = $account->badges ?: collect();
        $accountAgeInYears = $account->created_at->diffInYears(now());
        $accountAgeInYears = number_format((float) $accountAgeInYears, decimals: 2);

        $badge = new Badge;
        $badge->display_name = $accountAgeInYears.' years on PCB';
        $badge->unicode_icon = '⌚';
        $badges->add($badge);

        return $badges;
    }
}
