<?php

namespace Repositories;

use Entities\Models\Eloquent\MinecraftPlayer;

/**
 * @final
 * @deprecated
 */
class MinecraftPlayerRepository
{
    public function store(
        string $uuid,
        ?int $accountId = null,
    ): MinecraftPlayer {
        return MinecraftPlayer::create([
            'uuid' => $uuid,
            'account_id' => $accountId,
        ]);
    }

    public function getByUUID(string $uuid): ?MinecraftPlayer
    {
        return MinecraftPlayer::where('uuid', $uuid)->first();
    }
}
