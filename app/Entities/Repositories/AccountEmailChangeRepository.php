<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\AccountEmailChange;
use App\Repository;

/**
 * @deprecated Use AccountEmailChange model facade instead
 */
final class AccountEmailChangeRepository extends Repository
{
    protected $model = AccountEmailChange::class;

    public function create(
        int $accountId,
        string $token,
        string $previousEmail,
        string $newEmail
    ): AccountEmailChange {
        return $this->getModel()->create([
            'account_id' => $accountId,
            'token' => $token,
            'email_previous' => $previousEmail,
            'email_new' => $newEmail,
            'is_previous_confirmed' => false,
            'is_new_confirmed' => false,
        ]);
    }

    public function getByToken(string $token): ?AccountEmailChange
    {
        return $this->getModel()
            ->where('token', $token)
            ->first();
    }
}
