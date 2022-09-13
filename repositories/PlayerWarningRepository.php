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
        bool $isAcknowledged,
    ): PlayerWarning {
        return PlayerWarning::create([
            'warned_player_id' => $warnedPlayerId,
            'warner_player_id' => $warnerPlayerId,
            'reason' => $reason,
            'weight' => $weight,
            'is_acknowledged' => $isAcknowledged,
        ]);
    }

    public function all(int $playerId): Collection
    {
        return PlayerWarning::where('warned_player_id', $playerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find(int $warningId): ?PlayerWarning
    {
        return PlayerWarning::find($warningId);
    }

    public function acknowledge(PlayerWarning $warning)
    {
        $warning->is_acknowledged = true;
        $warning->acknowledged_at = now();
        $warning->save();
    }
}
