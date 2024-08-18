<?php

namespace Repositories;

use App\Models\AccountEmailChange;

/**
 * @final
 */
class AccountEmailChangeRepository
{
    public function create(
        int $accountId,
        string $token,
        string $previousEmail,
        string $newEmail
    ): AccountEmailChange {
        return AccountEmailChange::create([
            'account_id' => $accountId,
            'token' => $token,
            'email_previous' => $previousEmail,
            'email_new' => $newEmail,
            'is_previous_confirmed' => false,
            'is_new_confirmed' => false,
        ]);
    }

    public function firstByToken(string $token): ?AccountEmailChange
    {
        return AccountEmailChange::where('token', $token)->first();
    }
}
