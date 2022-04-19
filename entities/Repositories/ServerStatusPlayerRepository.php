<?php

namespace Entities\Repositories;

use Entities\Models\Eloquent\ServerStatusPlayer;

/**
 * @final
 */
class ServerStatusPlayerRepository
{
    public function store(
        int $serverStatusId,
        int $playerId,
        string $playerType,
    ): ServerStatusPlayer {
        return ServerStatusPlayer::create([
            'server_status_id' => $serverStatusId,
            'player_id' => $playerId,
            'player_type' => $playerType,
        ]);
    }
}
