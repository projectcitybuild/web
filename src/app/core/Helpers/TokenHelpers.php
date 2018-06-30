<?php
namespace App\Core;

class TokenHelpers {

    private function __construct() {}

    private const HASH_ALGORITHM = 'sha256';

    public static function generateToken($message = null) : string {
        // generate a token using either
        // the supplied data or use the
        // current timestamp
        $message = $message ?: time();

        $key   = env('APP_KEY');
        $token = hash_hmac(self::HASH_ALGORITHM, $message, $key);

        return $token;
    }

}