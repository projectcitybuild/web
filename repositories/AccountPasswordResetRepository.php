<?php

namespace Repositories;

use Carbon\Carbon;
use Entities\Models\Eloquent\AccountPasswordReset;

/**
 * @final
 */
class AccountPasswordResetRepository
{
    /**
     * Updates an AccountPasswordReset matching the given email. If not
     * found, creates a new one for the given email
     */
    public function updateByEmailOrCreate(string $email, string $token): AccountPasswordReset
    {
        return AccountPasswordReset::updateOrCreate(
            attributes: [
                'email' => $email,
            ],
            values: [
                'token' => $token,
                'created_at' => Carbon::now(),
            ],
        );
    }

    public function firstByToken(string $token): ?AccountPasswordReset
    {
        return AccountPasswordReset::where('token', $token)
            ->first();
    }

    public function deleteOlderThanOrEqualTo(Carbon $date)
    {
        return AccountPasswordReset::whereDate('created_at', '<=', $date)
            ->delete();
    }

    public function delete(AccountPasswordReset $passwordReset)
    {
        $passwordReset->delete();
    }
}
