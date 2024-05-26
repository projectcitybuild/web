<?php

namespace App\Domains\PasswordReset\Actions;

use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class SendPasswordResetLink
{
    /**
     * @throws ValidationException
     */
    public function call(string $email): void
    {
        $status = Password::sendResetLink([$email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }
}
