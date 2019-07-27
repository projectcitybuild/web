<?php

namespace App\Entities\Warnings\Repositories;

use App\Entities\Warnings\Models\GameWarning;
use App\Entities\Bans\Models\GameBan;
use App\Repository;

final class GameWarningRepository extends Repository
{
    protected $model = GameWarning::class;

    public function store(
        int $serverId,
        int $warnedPlayerId,
        string $warnedPlayerType,
        int $staffPlayerId,
        string $staffPlayerType,
        string $reason,
        int $weight = 1,
        bool $isActive = true
    ) : GameBan {
        return $this->getModel()->create([
            'server_id'             => $serverId,
            'warned_player_id'      => $warnedPlayerId,
            'warned_player_type'    => $warnedPlayerType,
            'staff_player_id'       => $staffPlayerId,
            'staff_player_type'     => $staffPlayerType,
            'reason'                => $reason,
            'weight'                => $weight,
            'is_active'             => $isActive,
        ]);
    }

    public function getCount(
        int $serverId,
        int $warnedPlayerId,
        string $warnedPlayerType
    ) : int {
        return $this->getModel()
            ->where('warned_player_id', $warnedPlayerId)
            ->where('warned_player_type', $warnedPlayerType)
            ->where('server_id', $serverId)
            ->count();
    }
}
