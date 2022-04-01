<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\GameUnban;
use App\Entities\Models\GamePlayerType;
use App\Repository;

/**
 * @deprecated Use GameUnban model facade instead
 */
final class GameUnbanRepository extends Repository
{
    protected $model = GameUnban::class;

    /**
     * Stores a new GameUnban.
     */
    public function store(int $banId, int $staffPlayerId, GamePlayerType $staffPlayerType): GameUnban
    {
        return $this->getModel()->create([
            'game_ban_id' => $banId,
            'staff_player_id' => $staffPlayerId,
            'staff_player_type' => $staffPlayerType->valueOf(),
        ]);
    }
}
