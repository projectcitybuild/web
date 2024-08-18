<?php

namespace Repositories;

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerAlias;
use DB;

/**
 * @deprecated
 */
final class PlayerFetchRepository
{
    public function createPlayerWithAlias(string $uuid, string $alias)
    {
        DB::beginTransaction();
        $player = MinecraftPlayer::create([
            'uuid' => $uuid,
            'account_id' => null,
            'last_synced_at' => now(),
        ]);
        MinecraftPlayerAlias::create([
            'player_minecraft_id' => $player->getKey(),
            'alias' => $alias,
            'registered_at' => now(),
        ]);
        DB::commit();
    }

    public function addAliasForPlayer(int $playerId, string $newAlias)
    {
        MinecraftPlayerAlias::create([
            'player_minecraft_id' => $playerId,
            'alias' => $newAlias,
            'registered_at' => now(),
        ]);
    }
}
