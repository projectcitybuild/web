<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\MinecraftPlayer;
use App\Entities\Players\Repositories\GameUser;
use App\Repository;
use Carbon\Carbon;

/**
 * @deprecated Use MinecraftPlayer model facade instead
 */
class MinecraftPlayerRepository extends Repository
{
    protected $model = MinecraftPlayer::class;

    /**
     * Creates a new MinecraftPlayer.
     *
     *
     * @return GameUser
     */
    public function store(string $uuid,
                          ?Carbon $lastSeenAt = null,
                          ?int $accountId = null
                          ): MinecraftPlayer {
        return $this->getModel()->create([
            'uuid' => $uuid,
            'account_id' => $accountId,
        ]);
    }

    public function getByUuid(string $uuid): ?MinecraftPlayer
    {
        return $this->getModel()
            ->where('uuid', $uuid)
            ->first();
    }

    public function getByAccountId(int $accountId): ?MinecraftPlayer
    {
        return $this->getModel()
            ->where('account_id', $accountId)
            ->first();
    }
}
