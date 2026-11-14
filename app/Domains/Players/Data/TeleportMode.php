<?php

namespace App\Domains\Players\Data;

enum TeleportMode: string
{
    case OFF = 'off';
    case REQUEST = 'request';
    case AUTO = 'auto';
}
