<?php

namespace App\Domains\MinecraftStats\Data;

class PlayerStatIncrement
{
    public function __construct(
        public readonly int $afkTime = 0,
        public readonly int $blocksPlaced = 0,
        public readonly int $blocksDestroyed = 0,
        public readonly int $blocksTravelled = 0,
        public readonly int $deaths = 0,
        public readonly int $mobKills = 0,
        public readonly int $playerKills = 0,
    ) {}

    public function isNonZero(): bool
    {
        return $this->afkTime > 0
            || $this->blocksPlaced > 0
            || $this->blocksDestroyed > 0
            || $this->blocksTravelled > 0
            || $this->deaths > 0
            || $this->mobKills > 0
            || $this->playerKills > 0;
    }

    public function toArray(): array
    {
        return [
            'afk_time' => $this->afkTime,
            'blocks_placed' => $this->blocksPlaced,
            'blocks_destroyed' => $this->blocksDestroyed,
            'blocks_travelled' => $this->blocksTravelled,
            'deaths' => $this->deaths,
            'mob_kills' => $this->mobKills,
            'player_kills' => $this->playerKills,
        ];
    }

    public static function random(): self
    {
        return new PlayerStatIncrement(
            afkTime: rand(0, 100_000_000),
            blocksPlaced: rand(0, 100_000_000),
            blocksDestroyed: rand(0, 100_000_000),
            blocksTravelled: rand(0, 100_000_000),
            deaths: rand(0, 100_000_000),
            mobKills: rand(0, 100_000_000),
            playerKills: rand(0, 100_000_000),
        );
    }
}
