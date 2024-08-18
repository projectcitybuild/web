<?php

namespace Repositories;

use App\Domains\ServerStatus\Entities\ServerQueryResult;
use App\Models\Server;
use Illuminate\Support\Carbon;

/**
 * @deprecated
 */
class ServerRepository
{
    public function getById(int $serverId): ?Server
    {
        return Server::find($serverId);
    }

    public function updateStatus(Server $server, ServerQueryResult $status, Carbon $queriedAt)
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
