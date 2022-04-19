<?php

namespace Domain\ServerStatus\Repositories;

use Domain\ServerStatus\Entities\ServerQueryResult;
use Entities\Models\Eloquent\ServerStatus;

final class ServerStatusRepository
{
    public function store(
        int $serverId,
        ServerQueryResult $result,
        int $time
    ): ServerStatus {
        return ServerStatus::create([
            'server_id' => $serverId,
            'is_online' => $result->isOnline,
            'num_of_players' => $result->numOfPlayers ?? 0,
            'num_of_slots' => $result->numOfSlots ?? 0,
        ]);
    }
}
