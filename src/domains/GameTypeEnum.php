<?php
namespace Domains;

use Domains\Enum;
use Domains\Library\QueryServer\ServerQueryAdapterContract;
use Domains\Library\QueryServer\GameAdapters\MinecraftQueryAdapter;
use Domains\Library\QueryPlayer\PlayerQueryAdapterContract;
use Domains\Library\QueryPlayer\GameAdapters\MojangUuidAdapter;

/**
 * List of games PCB supports
 */
final class GameTypeEnum extends Enum
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
