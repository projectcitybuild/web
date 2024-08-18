<?php

namespace App\Domains\Bans;

use App\Core\Utilities\Traits\ValueJoinable;

enum UnbanType: string
{
    use ValueJoinable;

    case EXPIRED = 'expired';
    case MANUAL = 'manual';
    case APPEALED = 'appealed';
    case CONVERTED_TO_PERMANENT = 'converted_to_permanent';
}
