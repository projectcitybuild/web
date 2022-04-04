<?php

namespace App\Entities\Models;

use App\Enum;

final class GameIdentifierType extends Enum
{
    const MinecraftUUID = 'minecraft_uuid';

    public function playerType(): GamePlayerType
    {
        return match ($this->value) {
            self::MinecraftUUID => GamePlayerType::MINECRAFT,
        };
    }
}
