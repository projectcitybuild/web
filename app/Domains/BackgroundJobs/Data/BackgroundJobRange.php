<?php

namespace App\Domains\BackgroundJobs\Data;

enum BackgroundJobRange: string
{
    case LAST_HOUR = '1h';
    case LAST_DAY = '24h';
    case LAST_WEEK = '7d';
    case LAST_MONTH = '30d';

    public function seconds(): int
    {
        return match ($this) {
            self::LAST_HOUR => 60 * 60,
            self::LAST_DAY => 24 * 60 * 60,
            self::LAST_WEEK => 7 * 24 * 60 * 60,
            self::LAST_MONTH => 30 * 24 * 60 * 60,
        };
    }

    public function bucketSeconds(): int
    {
        return match ($this) {
            self::LAST_HOUR => 60,
            self::LAST_DAY => 15 * 60,
            self::LAST_WEEK => 60 * 60,
            self::LAST_MONTH => 6 * 60 * 60,
        };
    }
}
