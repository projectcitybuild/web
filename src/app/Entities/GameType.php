<?php

namespace App\Entities;

use App\Enum;
use App\Library\QueryServer\ServerQueryAdapterContract;
use App\Library\QueryServer\GameAdapters\MinecraftQueryAdapter;
use App\Library\QueryPlayer\PlayerQueryAdapterContract;
use App\Library\QueryPlayer\GameAdapters\MojangUuidAdapter;

/**
 * List of games PCB supports
 */
final class GameType extends Enum
{
    public const Minecraft = 1;
    public const Terraria = 2;
    public const Starbound = 3;

    public function serverQueryAdapter() : ServerQueryAdapterContract
    {
        switch ($this->value) {
            case self::Minecraft:
                return resolve(MinecraftQueryAdapter::class);
            default:
                return null;
        }
    }

    public function playerQueryAdapter() : PlayerQueryAdapterContract
    {
        switch ($this->value) {
            case self::Minecraft:
                return resolve(MojangUuidAdapter::class);
            default:
                return null;
        }
    }

    public function name() : ?string
    {
        switch ($this->value) {
            case self::Minecraft:
                return 'minecraft';
            default:
                return null;
        }
    }
    
}
