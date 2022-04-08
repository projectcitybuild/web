<?php

namespace App\Helpers;

/**
 * @deprecated 
 */
final class TokenHelpers
{
    private const HASH_ALGORITHM = 'sha256';

    private function __construct() {}

    /**
     * @deprecated Use TokenGenerator interface instead
     */
    public static function generateToken($message = null): string
    {
        // generate a token using either the supplied data or use the
        // current timestamp
        $message = $message ? $message : time();

        $key = config('app.key');

        return hash_hmac(self::HASH_ALGORITHM, $message, $key);
    }
}
