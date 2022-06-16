<?php

namespace Repositories;

use Entities\Models\Eloquent\GameUnban;
use Entities\Models\GamePlayerType;

/**
 * @final
 */
class GameUnbanRepository
{
    public function create(
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
