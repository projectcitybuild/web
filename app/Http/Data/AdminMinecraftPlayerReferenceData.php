<?php

namespace App\Http\Data;

use App\Models\MinecraftPlayer;
use Spatie\LaravelData\Data;

class AdminMinecraftPlayerReferenceData extends Data
{
    public function __construct(
        public int $id,
        public ?string $alias,
    ) {}

    public static function fromModel(MinecraftPlayer $player): self
    {
        return new self(
            id: $player->id,
            alias: $player->alias,
        );
    }
}
