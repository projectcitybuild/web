<?php

namespace App\Domains\Bans\Data;

use App\Core\Utilities\Traits\ValueJoinable;

enum UnbanType: string
{
    use ValueJoinable;

    case EXPIRED = 'expired';
    case MANUAL = 'manual';
    case APPEALED = 'appealed';
}
