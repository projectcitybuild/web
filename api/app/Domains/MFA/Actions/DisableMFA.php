<?php

namespace App\Domains\MFA\Actions;

use App\Models\Account;

class DisableMFA
{
    public function call(Account $account): void
    {
        $account->two_factor_secret = null;
        $account->two_factor_recovery_codes = null;
        $account->two_factor_confirmed_at = null;
        $account->save();
    }
}
