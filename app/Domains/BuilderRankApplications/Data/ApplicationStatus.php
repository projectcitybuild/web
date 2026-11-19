<?php

namespace App\Domains\BuilderRankApplications\Data;

enum ApplicationStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DENIED = 'denied';

    public function humanReadable(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::DENIED => 'Denied',
        };
    }
}
