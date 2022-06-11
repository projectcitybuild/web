<?php

namespace Repositories;

use Domain\ServerStatus\Entities\ServerQueryResult;
use Entities\Models\Eloquent\Server;
use Entities\Models\Eloquent\ServerStatus;
use Illuminate\Support\Carbon;

final class ServerStatusRepository
{
    public function update(Server $server, ServerQueryResult $status, Carbon $queriedAt)
    {
        if ($status->isOnline) {
            $server->num_of_players = $status->numOfPlayers;
            $server->num_of_slots = $status->numOfSlots;
        } else {
            $server->num_of_players = 0;
            $server->num_of_slots = 0;
        }
        $server->is_online = $status->isOnline;
        $server->last_queried_at = $queriedAt;
        $server->save();
    }
}
