<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\ServerStatusPlayer;
use App\Repository;

/**
 * @deprecated Use ServerStatusPlayer model facade instead
 */
class ServerStatusPlayerRepository extends Repository
{
    protected $model = ServerStatusPlayer::class;

    public function store(int $serverStatusId,
                          int $playerId,
                          string $playerType): ServerStatusPlayer
    {
        return $this->getModel()->create([
            'server_status_id' => $serverStatusId,
            'player_id' => $playerId,
            'player_type' => $playerType,
        ]);
    }
}
