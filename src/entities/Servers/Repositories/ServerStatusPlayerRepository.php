<?php
namespace Entities\Servers\Repositories;

use Entities\Servers\Models\ServerStatusPlayer;
use Carbon\Carbon;
use Application\Repository;

class ServerStatusPlayerRepository extends Repository
{
    protected $model = ServerStatusPlayer::class;

    public function store(int $serverStatusId,
                          int $playerId,
                          string $playerType) : ServerStatusPlayer
    {
        return $this->getModel()->create([
            'server_status_id'  => $serverStatusId,
            'player_id'         => $playerId,
            'player_type'       => $playerType,
        ]);
    }
}
