<?php

namespace App\Domains\Badges\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\Badge;
use App\Models\MinecraftPlayer;

final class GetBadges
{
    public function execute(MinecraftUUID $uuid): array
    {
        $player = MinecraftPlayer::whereUuid($uuid)->first();
        $account = $player->account;

        if ($account === null) {
            return [];
        }

        $badges = $account->badges;
        $accountAgeInYears = $account->created_at->diffInYears(now());
        $accountAgeInYears = number_format((float)$accountAgeInYears, decimals: 2);

        $badge = new Badge();
        $badge->display_name = $accountAgeInYears.' years on PCB';
        $badge->unicode_icon = '⌚';
        $badges->add($badge);

        return $badges->toArray();
    }
}
