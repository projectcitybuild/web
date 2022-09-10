<?php

namespace Repositories;

use Entities\Models\Eloquent\GameUnban;

/**
 * @final
 *
 * @deprecated
 */
class GameUnbanRepository
{
    public function create(
        int $banId,
        int $staffPlayerId,
    ): GameUnban {
        return GameUnban::create([
            'game_ban_id' => $banId,
            'staff_player_id' => $staffPlayerId,
        ]);
    }
}
