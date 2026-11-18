<?php

namespace App\Http\Data;

use App\Models\PlayerOpElevation;
use Spatie\LaravelData\Data;

class AdminPlayerOpElevationData extends Data
{
    public function __construct(
        public int $id,
        public AdminMinecraftPlayerReferenceData $player,
        public string $reason,
        public string $startedAt,
        public string $endedAt,
        public bool $isActive,
    ) {}

    public static function fromModel(PlayerOpElevation $elevation): self
    {
        return new self(
            id: $elevation->id,
            player: AdminMinecraftPlayerReferenceData::fromModel($elevation->player),
            reason: $elevation->reason,
            startedAt: $elevation->started_at->toIso8601String(),
            endedAt: $elevation->ended_at->toIso8601String(),
            isActive: $elevation->ended_at->isFuture(),
        );
    }
}
