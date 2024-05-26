<?php

namespace App\Domains\Bans;

enum UnbanType: string
{
    case EXPIRED = 'expired';
    case MANUAL = 'manual';
    case APPEALED = 'appealed';
}
