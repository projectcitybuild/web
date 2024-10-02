<?php

namespace App\Core\Data;

/**
 * List of games PCB supports.
 * @deprecated
 */
enum GameType: int
{
    case MINECRAFT = 1;
    case TERRARIA = 2;

    public function name(): ?string
    {
        return match ($this) {
            self::MINECRAFT => 'minecraft',
            self::TERRARIA => 'terraria',
        };
    }
}
