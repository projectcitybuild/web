<?php

namespace App\Entities\Repositories;

use App\Entities\MinecraftUUID;
use App\Entities\Models\Eloquent\MinecraftPlayer;

/**
 * @final
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

    public function getByUuid(MinecraftUUID $uuid): ?MinecraftPlayer
    {
        return MinecraftPlayer::where('uuid', $uuid->rawValue())->first();
    }

    public function getByAccountId(int $accountId): ?MinecraftPlayer
    {
        return MinecraftPlayer::where('account_id', $accountId)->first();
    }
}
