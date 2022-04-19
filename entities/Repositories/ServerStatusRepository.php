<?php

namespace Entities\Repositories;

use Carbon\Carbon;
use Entities\Models\Eloquent\ServerStatus;

/**
 * @final
 */
class ServerStatusRepository
{
    public function create(
        int $serverId,
        bool $isOnline,
        int $numOfPlayers,
        int $numOfSlots,
        int $createdAt,
    ): ServerStatus {
        return ServerStatus::create([
            'server_id' => $serverId,
            'is_online' => $isOnline,
            'num_of_players' => $numOfPlayers,
            'num_of_slots' => $numOfSlots,
            'created_at' => Carbon::createFromTimestamp($createdAt),
        ]);
    }
}
