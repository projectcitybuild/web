<?php

namespace Domain\Bans;

enum UnbanType: string
{
    case EXPIRED = 'expired';
    case MANUAL = 'manual';
    case APPEALED = 'appealed';
    case CONVERTED_TO_PERMANENT = 'converted_to_permanent';
}
