<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;

final class GetActivePlayerBan
{
    public function execute(MinecraftUUID $uuid): ?GamePlayerBan
    {
        $player = MinecraftPlayer::whereUuid($uuid)->first();
        if ($player === null) {
            return null;
        }

        return GamePlayerBan::where('banned_player_id', $player->getKey())
            ->active()
            ->first();
    }
}
