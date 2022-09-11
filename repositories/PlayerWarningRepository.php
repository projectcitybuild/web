<?php

namespace Repositories;

use Entities\Models\Eloquent\PlayerWarning;

final class PlayerWarningRepository
{
    public function create(
        int $warnedPlayerId,
        int $warnerPlayerId,
        string $reason,
        float $weight,
    ): PlayerWarning {
        return PlayerWarning::create([
            'warned_player_id' => $warnedPlayerId,
            'warner_player_id' => $warnerPlayerId,
            'reason' => $reason,
            'weight' => $weight,
            'is_active' => true,
        ]);
    }
}
