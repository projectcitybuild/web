<?php

namespace Repositories\PlayerWarnings;

use App\Models\PlayerWarning;
use Illuminate\Support\Collection;

interface PlayerWarningRepository
{
    public function create(
        int $warnedPlayerId,
        int $warnerPlayerId,
        string $reason,
        float $weight,
        bool $isAcknowledged,
    ): PlayerWarning;

    public function all(int $playerId): Collection;

    public function find(int $warningId): ?PlayerWarning;

    public function acknowledge(PlayerWarning $warning): PlayerWarning;
}
