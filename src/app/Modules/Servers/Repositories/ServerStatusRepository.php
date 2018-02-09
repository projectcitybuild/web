<?php
namespace App\Modules\Servers\Repositories;

use App\Modules\Servers\Models\ServerStatus;
use Carbon\Carbon;

class ServerStatusRepository {

    private $statusModel;

    public function __construct(ServerStatus $statusModel) {
        $this->statusModel = $statusModel;
    }

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
    public function create(int $serverId, bool $isOnline, int $numOfPlayers, int $numOfSlots, int $createdAt) {
        return $this->statusModel->create([
            'server_id'         => $serverId,
            'is_online'         => $isOnline,
            'num_of_players'    => $numOfPlayers,
            'num_of_slots'      => $numOfSlots,
            'created_at'        => Carbon::createFromTimestamp($createdAt),
        ]);
    }

}