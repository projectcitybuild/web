<?php

namespace App\Core\Domains\Discord;

final class DiscordSnowflake
{
    public const VALIDATION_PATTERN = '/\A[0-9]{17,20}\z/';
    public const ROUTE_PATTERN = '[0-9]{17,20}';

    public static function isValid(?string $value): bool
    {
        return is_string($value) && preg_match(self::VALIDATION_PATTERN, $value) === 1;
    }
}
