<?php
namespace Domains\Modules\Servers;

use Infrastructure\Enum;
use Domains\Library\QueryServer\GameAdapters\MinecraftQueryAdapter;

/**
 * List of games PCB supports
 */
final class GameTypeEnum extends Enum
{
    public const Minecraft = 1;
    public const Terraria = 2;
    public const Starbound = 3;

    public function serverQueryAdapter() : ?string
    {
        switch ($this->value) {
            case self::Minecraft:
                return MinecraftQueryAdapter::class;
            default:
                return null;
        }
    }

    public function playerQueryAdapter() : ?string
    {
        switch ($this->value) {
            default:
                return null;
        }
    }
    
}
