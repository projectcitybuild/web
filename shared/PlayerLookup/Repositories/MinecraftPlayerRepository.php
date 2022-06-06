<?php

namespace Shared\PlayerLookup\Repositories;

use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\MinecraftUUID;

/**
 * @final
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
}
