<?php

namespace App\Domains\MinecraftStats\Data;

class PlayerStatIncrement
{
    public function __construct(
        public readonly int $afkTime,
        public readonly int $blocksPlaced,
        public readonly int $blocksDestroyed,
        public readonly int $blocksTravelled,
    ) {}

    public function isNonZero(): bool
    {
        return $this->afkTime > 0
            || $this->blocksPlaced > 0
            || $this->blocksDestroyed > 0
            || $this->blocksTravelled > 0;
    }
}
