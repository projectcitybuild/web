<?php

namespace Repositories;

use App\Models\PasswordReset;
use Carbon\Carbon;

/**
 * @deprecated
 */
class AccountPasswordResetRepository
{
    /**
     * Updates an AccountPasswordReset matching the given email. If not
     * found, creates a new one for the given email
     */
    public function updateByEmailOrCreate(string $email, string $token): PasswordReset
    {
        return PasswordReset::updateOrCreate(
            attributes: [
                'email' => $email,
            ],
            values: [
                'token' => $token,
                'created_at' => Carbon::now(),
            ],
        );
    }

    public function firstByToken(string $token): ?PasswordReset
    {
        return PasswordReset::where('token', $token)
            ->first();
    }

    public function deleteOlderThanOrEqualTo(Carbon $date)
    {
        return PasswordReset::whereDate('created_at', '<=', $date)
            ->delete();
    }

    public function delete(PasswordReset $passwordReset)
    {
        $passwordReset->delete();
    }
}
