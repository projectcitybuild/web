<?php

namespace App\Entities\Models;

enum GameIdentifierType: string
{
    case MINECRAFT_UUID = 'minecraft_uuid';

    public function playerType(): GamePlayerType
    {
        return match ($this) {
            self::MINECRAFT_UUID => GamePlayerType::MINECRAFT,
        };
    }

    public static function joined(): string
    {
        return collect(self::cases())
            ->map(fn ($c) => $c->value)
            ->join(glue: ',');
    }
}
