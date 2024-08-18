<?php

namespace App\Domains\BuilderRankApplications\Entities;

enum ApplicationStatus: int
{
    case IN_PROGRESS = 1;
    case APPROVED = 2;
    case DENIED = 3;

    public function humanReadable(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'In progress',
            self::APPROVED => 'Approved',
            self::DENIED => 'Denied',
        };
    }
}
