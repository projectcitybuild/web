<?php
namespace App\Modules\Servers\Repositories;

use App\Modules\Servers\Models\ServerStatusPlayer;
use Carbon\Carbon;
use App\Support\Repository;

class ServerStatusPlayerRepository extends Repository
{
    protected $model = ServerStatusPlayer::class;

    public function store(
        int $serverStatusId,
        int $playerId,
        string $playerType
    ) : ServerStatusPlayer {
        return $this->getModel()->create([
            'server_status_id'  => $serverStatusId,
            'player_id'         => $playerId,
            'player_type'       => $playerType,
        ]);
    }
}
