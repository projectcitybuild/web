<?php

namespace App\Models;

enum UnbanType: string
{
    case EXPIRED = 'expired';
    case MANUAL = 'manual';
    case APPEALED = 'appealed';
}
