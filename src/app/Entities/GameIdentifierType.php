<?php

namespace App\Entities;

use App\Enum;
use App\Entities\Players\Models\MinecraftPlayer;

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

    /**
     * Maps game identifier types (eg. MINECRAFT_UUID) to game player type (eg. MINECRAFT)
     *
     * @var array
     */
    public static $identifierMapping = [
        GameIdentifierType::MinecraftUUID => MinecraftPlayer::class,
    ];

    /**
     * Returns the identifier mapping array joined by a "," separator
     *
     * @return string
     */
    public static function identifierMappingStr() : string 
    {
        return implode(',', array_keys(self::$identifierMapping));
    }

}