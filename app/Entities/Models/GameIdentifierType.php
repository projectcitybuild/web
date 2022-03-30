<?php

namespace App\Entities\Models;

use App\Enum;

final class GameIdentifierType extends Enum
{
    const MinecraftUUID = 'minecraft_uuid';

    public function playerType()
    {
        switch ($this->value) {
            case self::MinecraftUUID:
                return GamePlayerType::Minecraft();
        }
    }
}
