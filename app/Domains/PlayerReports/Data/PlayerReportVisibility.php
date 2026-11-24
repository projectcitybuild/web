<?php

namespace App\Domains\PlayerReports\Data;

enum PlayerReportVisibility: string
{
    case STANDARD = 'standard';
    case ADMINISTRATORS = 'administrators';
}
