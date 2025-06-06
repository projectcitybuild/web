<?php

namespace App\Domains\Mfa;

use App\Domains\Mfa\Exceptions\Dangerous2FABypassException;
use Exception;
use PragmaRX\Google2FA\Google2FA;

class Google2FAFake extends Google2FA
{
    /**
     * @throws Exception if called with the wrong environment variables
     */
    public function verifyKeyNewer($secret, $key, $oldTimestamp, $window = null, $timestamp = null): bool|int
    {
        if (config('app.debug') !== true || config('auth.totp.bypass') !== true) {
            throw new Dangerous2FABypassException();
        }

        return $key == '0000' ? $this->getTimestamp() : false;
    }
}
