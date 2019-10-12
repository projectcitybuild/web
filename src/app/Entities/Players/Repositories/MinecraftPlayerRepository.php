<?php
namespace App\Entities\Players\Repositories;

use App\Entities\Players\Models\MinecraftPlayer;
use App\Repository;
use Carbon\Carbon;

/**
 * @deprecated Use MinecraftPlayer model facade instead
 */
class MinecraftPlayerRepository extends Repository
{
    protected $model = MinecraftPlayer::class;

    /**
     * Creates a new MinecraftPlayer
     *
     * @param int $userId
     * @return GameUser
     */
    public function store(string $uuid,
                          ?Carbon $lastSeenAt = null,
                          ?int $accountId = null,
                          int $playTime = 0) : MinecraftPlayer 
    {
        return $this->getModel()->create([
            'uuid'          => $uuid,
            'account_id'    => $accountId,
            'playtime'      => $playTime,
            'last_seen_at'  => $lastSeenAt ?: now(),
        ]);
    }

    public function getByUuid(string $uuid) : ?MinecraftPlayer
    {
        return $this->getModel()
            ->where('uuid', $uuid)
            ->first();
    }

    public function getByAccountId(int $accountId) : ?MinecraftPlayer
    {
        return $this->getModel()
            ->where('account_id', $accountId)
            ->first();
    }
}
