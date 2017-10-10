<?php
namespace App\Modules\Servers;

/**
 * List of games PCB supports
 */
class GameTypeEnum extends \SplEnum {
    const __default = self::UNKNOWN;

    const UNKNOWN = 0;
    const Minecraft = 1;
    const Terraria = 2;
    const Starbound = 3;
}