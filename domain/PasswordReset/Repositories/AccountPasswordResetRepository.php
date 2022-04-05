<?php

namespace Domain\PasswordReset\Repositories;

use App\Entities\Models\Eloquent\AccountPasswordReset;
use Carbon\Carbon;

/**
 * @final
 */
class AccountPasswordResetRepository
{
    public function updateOrCreateByEmail(string $email, string $token): AccountPasswordReset
    {
        return AccountPasswordReset::updateOrCreate([
            'email' => $email,
        ], [
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);
    }

    public function getByEmail(string $email): ?AccountPasswordReset
    {
        return AccountPasswordReset::where('email', $email)
            ->first();
    }

    public function getByToken(string $token): ?AccountPasswordReset
    {
        return AccountPasswordReset::where('token', $token)
            ->first();
    }

    public function deleteOlderThan(Carbon $date)
    {
        return AccountPasswordReset::whereDate('created_at', '<', $date)
            ->delete();
    }
}
