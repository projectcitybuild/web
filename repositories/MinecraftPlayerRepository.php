<?php

namespace Repositories;

use App\Core\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;

/**
 * @deprecated
 */
class MinecraftPlayerRepository
{
    public function store(
        MinecraftUUID $uuid,
        ?int $accountId = null,
    ): MinecraftPlayer {
        return MinecraftPlayer::create([
            'uuid' => $uuid->rawValue(),
            'account_id' => $accountId,
        ]);
    }

    public function getByUUID(MinecraftUUID $uuid): ?MinecraftPlayer
    {
        return MinecraftPlayer::where('uuid', $uuid->rawValue())->first();
    }

    public function getById(int $id): ?MinecraftPlayer
    {
        return MinecraftPlayer::where('player_minecraft_id', $id)->first();
    }
}
