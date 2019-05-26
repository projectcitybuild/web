<?php

namespace App\Entities\Players\Repositories;

use App\Entities\Players\Models\MinecraftAuthCode;
use App\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Ramsey\Uuid\UuidInterface;

final class MinecraftAuthCodeRepository extends Repository
{
    protected $model = MinecraftAuthCode::class;

    public function store(int $minecraftPlayerId, string $minecraftUuid, string $token, Carbon $expiresAt) : MinecraftAuthCode
    {
        return $this->getModel()->create([
            'player_minecraft_id' => $minecraftPlayerId,
            'uuid' => $minecraftUuid,
            'token' => $token,
            'expires_at' => $expiresAt,           
        ]);
    }

    public function deleteByMinecraftPlayerId(int $minecraftPlayerId)
    {
        $this->getModel()
            ->where('player_minecraft_id', $minecraftPlayerId)
            ->delete();
    }
}
