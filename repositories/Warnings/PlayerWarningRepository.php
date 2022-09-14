<?php

namespace Repositories\Warnings;

use Entities\Models\Eloquent\PlayerWarning;
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
