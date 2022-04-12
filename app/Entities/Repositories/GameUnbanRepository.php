<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\GameUnban;
use App\Entities\Models\GamePlayerType;

/**
 * @final
 */
class GameUnbanRepository
{
    public function store(
        int $banId,
        int $staffPlayerId,
        GamePlayerType $staffPlayerType,
    ): GameUnban {
        return GameUnban::create([
            'game_ban_id' => $banId,
            'staff_player_id' => $staffPlayerId,
            'staff_player_type' => $staffPlayerType->value,
        ]);
    }
}
