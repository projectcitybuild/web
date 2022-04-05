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
}
