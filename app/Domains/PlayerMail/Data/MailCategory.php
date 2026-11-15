<?php

namespace App\Domains\PlayerMail\Data;

enum MailCategory: string
{
    case DIRECT = 'direct';
    case ANNOUNCEMENT = 'announcement';
    case SYSTEM = 'system';
}
