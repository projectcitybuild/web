<?php

namespace Repositories;

use Entities\Models\Eloquent\PlayerWarning;
use Illuminate\Database\Eloquent\Collection;

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
        ]);
    }

    public function all(int $playerId): Collection
    {
        return PlayerWarning::where('warned_player_id', $playerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
