<?php

namespace App\Domains\Players\Data;

enum MoveMode: string
{
    case WALK = 'walk';
    case FLY = 'fly';
}
