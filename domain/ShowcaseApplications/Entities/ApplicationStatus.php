<?php

namespace Domain\ShowcaseApplications\Entities;

use Illuminate\Support\Str;

enum ApplicationStatus: int
{
    case PENDING = 1;
    case APPROVED = 2;
    case DENIED = 3;

    public function isDecided(): bool
    {
        return $this != ApplicationStatus::PENDING;
    }

    public function humanReadable(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::DENIED => 'Denied',
        };
    }

    public function humanReadableAction()
    {
        return match ($this) {
            self::PENDING => 'Pending Decision',
            self::APPROVED => 'Approve and Create Warp',
            self::DENIED => 'Deny and Close',
        };
    }

    public static function decisionCases()
    {
        return [self::APPROVED, self::DENIED];
    }

    public function slug()
    {
        return Str::slug($this->humanReadable());
    }
}
