<?php

namespace Repositories;

use App\Models\EmailChange;

/**
 * @deprecated
 */
class AccountEmailChangeRepository
{
    public function create(
        int $accountId,
        string $token,
        string $previousEmail,
        string $newEmail
    ): EmailChange {
        return EmailChange::create([
            'account_id' => $accountId,
            'token' => $token,
            'email_previous' => $previousEmail,
            'email_new' => $newEmail,
            'is_previous_confirmed' => false,
            'is_new_confirmed' => false,
        ]);
    }

    public function firstByToken(string $token): ?EmailChange
    {
        return EmailChange::where('token', $token)->first();
    }
}
