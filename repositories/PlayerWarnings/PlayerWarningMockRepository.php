<?php

namespace Repositories\PlayerWarnings;

use Entities\Models\Eloquent\PlayerWarning;
use Illuminate\Support\Collection;

final class PlayerWarningMockRepository implements PlayerWarningRepository
{
    public PlayerWarning $create;
    public Collection $all;
    public ?PlayerWarning $find;

    public function create(
        int $warnedPlayerId,
        int $warnerPlayerId,
        string $reason,
        float $weight,
        bool $isAcknowledged,
    ): PlayerWarning {
        return $this->create;
    }

    public function all(int $playerId): Collection
    {
        return $this->all;
    }

    public function find(int $warningId): ?PlayerWarning
    {
        return $this->find;
    }

    public function acknowledge(PlayerWarning $warning): PlayerWarning
    {
        $warning->is_acknowledged = true;
        $warning->acknowledged_at = now();

        return $warning;
    }
}
