<?php

namespace Domain\PasswordReset;

use Illuminate\Support\Facades\URL;

/**
 * @final
 */
class PasswordResetURLGenerator
{
    public function make(string $token): string
    {
        return URL::temporarySignedRoute(
            name: 'front.password-reset.edit',
            expiration: now()->addMinutes(20),
            parameters: ['token' => $token],
        );
    }
}
