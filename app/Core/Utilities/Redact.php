<?php

namespace App\Core\Utilities;

class Redact
{
    private const REDACTED_CHAR = '*';

    private function __construct() {}

    /**
     * Redacts emails so that only the first letter and domain are visible
     */
    public static function email(string $email): string
    {
        if ($email === '') {
            return '';
        }

        [$name, $domain] = explode('@', $email);

        return substr($name, 0, 1)
            .str_repeat(self::REDACTED_CHAR, max(strlen($name) - 1, 3))
            .'@'
            .$domain;
    }
}
