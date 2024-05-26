<?php

namespace App\Domains\MFA\Actions;

use RobThree\Auth\TwoFactorAuth;

class VerifyMFACode
{
    public function __construct(
        private readonly TwoFactorAuth $auth,
    ) {}

    public function call(string $secret, string $code): bool
    {
        return $this->auth->verifyCode(
            secret: decrypt($secret),
            code: $code,
        );
    }
}
