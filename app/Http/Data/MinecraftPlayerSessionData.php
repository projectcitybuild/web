<?php

namespace App\Http\Data;

use App\Models\MinecraftPlayerSession;
use Spatie\LaravelData\Data;

class MinecraftPlayerSessionData extends Data
{
    public function __construct(
        public int $id,
        public int $player_id,
        public int $seconds,
        public string $starts_at,
        public string $ends_at,
    ) {}

    public static function fromModel(MinecraftPlayerSession $session): self
    {
        return new self(
            id: $session->id,
            player_id: $session->player_id,
            seconds: $session->seconds,
            starts_at: $session->starts_at->toIso8601String(),
            ends_at: $session->ends_at?->toIso8601String(),
        );
    }
}
