<?php

namespace Repositories;

use Entities\Models\Eloquent\GameWarning;

final class GameWarningRepository
{
    public function create(
        int $warnedPlayerId,
        int $warnerPlayerId,
        string $reason,
        float $weight,
    ): GameWarning {
        return GameWarning::create([
            'warned_player_id' => $warnedPlayerId,
            'warner_player_id' => $warnerPlayerId,
            'reason' => $reason,
            'weight' => $weight,
            'is_active' => true,
        ]);
    }

    public function getTotalWeight(int $playerId): float
    {
        return GameWarning::where('warned_player_id', $playerId)
            ->whereDate('created_at', '>=', now()->subMonths(3))
            ->sum('weight');
    }
}
