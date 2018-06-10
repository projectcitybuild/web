<?php
namespace App\Modules\Servers;

use App\core\Enum;

/**
 * List of games PCB supports
 */
abstract class GameTypeEnum extends Enum {
    public const Minecraft = 1;
    public const Terraria = 2;
    public const Starbound = 3;
}