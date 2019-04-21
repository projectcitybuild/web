<?php
namespace Entities\Servers\Repositories;

use Entities\Servers\Models\ServerStatus;
use Application\Contracts\Repository;
use Carbon\Carbon;

class ServerStatusRepository extends Repository
{
    protected $model = ServerStatus::class;

    /**
     * Creates a new server status
     *
     * @param int $serverId
     * @param bool $isOnline
     * @param int $numOfPlayers
     * @param int $numOfSlots
     * @param int $createdAt
     * @return void
     */
    public function create(int $serverId, 
                           bool $isOnline, 
                           int $numOfPlayers, 
                           int $numOfSlots, 
                           int $createdAt)
    {
        return $this->getModel()->create([
            'server_id'         => $serverId,
            'is_online'         => $isOnline,
            'num_of_players'    => $numOfPlayers,
            'num_of_slots'      => $numOfSlots,
            'created_at'        => Carbon::createFromTimestamp($createdAt),
        ]);
    }
}
