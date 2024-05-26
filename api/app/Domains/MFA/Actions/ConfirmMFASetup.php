<?php

namespace App\Domains\MFA\Actions;

use App\Models\Account;

class ConfirmMFASetup
{
    public function call(Account $account): void
    {
        $account->two_factor_confirmed_at = now();
        $account->save();
    }
}
