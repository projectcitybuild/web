<?php

namespace App\Domains\PlayerMail\Data;

enum PlayerMailBroadcastStatus: string
{
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
