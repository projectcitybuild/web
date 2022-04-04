<?php

namespace App\Entities\Models;

/**
 * List of games PCB supports.
 */
enum GameType
{
    case MINECRAFT;
    case TERRARIA;
    case STARBOUND;

    public static function fromValue(int $value): ?GameType
    {
        return match ($value) {
            1 => self::MINECRAFT,
            2 => self::TERRARIA,
            3 => self::STARBOUND,
            default => null,
        };
    }

    public function value(): int
    {
        return match ($this) {
            self::MINECRAFT => 1,
            self::TERRARIA => 2,
            self::STARBOUND => 3,
        };
    }

    public function name(): ?string
    {
        return match ($this) {
            self::MINECRAFT => 'minecraft',
            default => null,
        };
    }
}
