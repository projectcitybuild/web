<?php

namespace App\Domains\PlayerReports\Data;

enum PlayerReportStatus: string
{
    case OPEN = 'open';
    case COMPLETED = 'completed';
}
