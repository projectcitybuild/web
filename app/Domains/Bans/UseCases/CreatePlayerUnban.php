<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\Bans\Exceptions\NotBannedException;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;

class CreatePlayerUnban
{
    public function execute(
        MinecraftUUID $bannedPlayerUuid,
        MinecraftUUID $unbannerPlayerUuid,
        UnbanType $unbanType,
    ): GamePlayerBan {
        $player = MinecraftPlayer::whereUuid($bannedPlayerUuid)->first()
            ?? throw new NotBannedException();

        $existingBan = GamePlayerBan::where('banned_player_id', $player->getKey())
            ->active()
            ->first()
            ?? throw new NotBannedException();

        $unbannerPlayer = MinecraftPlayer::whereUuid($unbannerPlayerUuid)->first();

        $existingBan->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayer->getKey(),
            'unban_type' => $unbanType->value,
        ]);

        return $existingBan->refresh();
    }
}
