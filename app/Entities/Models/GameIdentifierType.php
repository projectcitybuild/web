<?php

namespace App\Entities\Models;

use Helpers\ValueJoinable;

enum GameIdentifierType: string
{
    use ValueJoinable;

    case MINECRAFT_UUID = 'minecraft_uuid';

    public function playerType(): GamePlayerType
    {
        return match ($this) {
            self::MINECRAFT_UUID => GamePlayerType::MINECRAFT,
        };
    }
}
