<?php

namespace Domain\BanAppeals\Entities;

use Illuminate\Support\Str;

enum BanAppealStatus: int
{
    case PENDING = 0;
    case ACCEPTED_UNBAN = 1;
    case ACCEPTED_TEMPBAN = 2;
    case DENIED = 3;

    public function isDecided(): bool
    {
        return $this != BanAppealStatus::PENDING;
    }

    public function humanReadable(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACCEPTED_UNBAN => 'Unbanned',
            self::ACCEPTED_TEMPBAN => 'Tempbanned',
            self::DENIED => 'Denied',
        };
    }

    public function humanReadableAction()
    {
        return match ($this) {
            self::PENDING => 'Pending Decision',
            self::ACCEPTED_UNBAN => 'Unban',
            self::ACCEPTED_TEMPBAN => 'Convert to tempban',
            self::DENIED => 'Deny and keep banned',
        };
    }

    public static function decisionCases()
    {
        return [self::ACCEPTED_UNBAN, self::DENIED];
    }

    public function slug()
    {
        return Str::slug($this->humanReadable());
    }
}
