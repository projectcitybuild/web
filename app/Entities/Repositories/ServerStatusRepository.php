<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\ServerStatus;
use Carbon\Carbon;

class ServerStatusRepository
{
    /**
     * Creates a new server status
     */
    public function create(
        int $serverId,
        bool $isOnline,
        int $numOfPlayers,
        int $numOfSlots,
        int $createdAt,
    ) {
        return ServerStatus::create([
            'server_id' => $serverId,
            'is_online' => $isOnline,
            'num_of_players' => $numOfPlayers,
            'num_of_slots' => $numOfSlots,
            'created_at' => Carbon::createFromTimestamp($createdAt),
        ]);
    }
}
