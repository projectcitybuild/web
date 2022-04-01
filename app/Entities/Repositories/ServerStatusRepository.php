<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\ServerStatus;
use App\Repository;
use Carbon\Carbon;

/**
 * @deprecated Use ServerStatus model facade instead
 */
class ServerStatusRepository extends Repository
{
    protected $model = ServerStatus::class;

    /**
     * Creates a new server status.
     *
     *
     * @return void
     */
    public function create(int $serverId,
                           bool $isOnline,
                           int $numOfPlayers,
                           int $numOfSlots,
                           int $createdAt)
    {
        return $this->getModel()->create([
            'server_id' => $serverId,
            'is_online' => $isOnline,
            'num_of_players' => $numOfPlayers,
            'num_of_slots' => $numOfSlots,
            'created_at' => Carbon::createFromTimestamp($createdAt),
        ]);
    }
}
