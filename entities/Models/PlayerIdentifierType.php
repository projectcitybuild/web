<?php

namespace Entities\Models;

use Helpers\ValueJoinable;

enum PlayerIdentifierType: string
{
    use ValueJoinable;

    case MINECRAFT_UUID = 'minecraft_uuid';
    case PCB_PLAYER_ID = 'pcb_player_id';
}
