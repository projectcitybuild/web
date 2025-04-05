<?php

namespace App\Domains\BuilderRankApplications\Data;

enum ApplicationStatus: int
{
    case PENDING = 1;
    case APPROVED = 2;
    case DENIED = 3;

    public function humanReadable(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::DENIED => 'Denied',
        };
    }
}
