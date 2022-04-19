<?php

namespace Entities\Models;

/**
 * List of games PCB supports.
 */
enum GameType: int
{
    case MINECRAFT = 1;
    case TERRARIA = 2;
    case STARBOUND = 3;

    public function name(): ?string
    {
        return match ($this) {
            self::MINECRAFT => 'minecraft',
            default => null,
        };
    }
}
