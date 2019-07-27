<?php

namespace App\Entities\Bans\Repositories;

use App\Repository;
use App\Entities\Bans\Models\GameUnban;
use App\Entities\GamePlayerType;

final class GameUnbanRepository extends Repository
{
    protected $model = GameUnban::class;

    /**
     * Stores a new GameUnban
     *
     * @param integer $banId
     * @param integer $staffPlayerId
     * @param GamePlayerType $staffPlayerType
     *
     * @return GameUnban
     */
    public function store(int $banId, int $staffPlayerId, GamePlayerType $staffPlayerType) : GameUnban
    {
        return $this->getModel()->create([
            'game_ban_id'           => $banId,
            'staff_player_id'       => $staffPlayerId,
            'staff_player_type'     => $staffPlayerType->valueOf(),
        ]);
    }
}
