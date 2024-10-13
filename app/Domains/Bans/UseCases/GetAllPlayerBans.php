<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Collection;

final class GetAllPlayerBans
{
    public function execute(MinecraftUUID $uuid): Collection
    {
        $player = MinecraftPlayer::whereUuid($uuid)->first();
        if ($player === null) {
            return collect();
        }
        return GamePlayerBan::where('banned_player_id', $player->getKey())
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->get();
    }
}
