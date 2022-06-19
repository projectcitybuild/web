<?php

namespace Entities\Models;

use Helpers\ValueJoinable;

enum GameIdentifierType: string
{
    use ValueJoinable;

    case MINECRAFT_UUID = 'minecraft_uuid';
}
