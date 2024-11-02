<?php

namespace App\Http\Controllers\Manage;

use App\Domains\Activation\Notifications\AccountNeedsActivationNotification;
use App\Models\Account;

class AccountResendActivation
{
    public function __invoke(Account $account)
    {
        $account->notify(new AccountNeedsActivationNotification($account));

        return redirect()->back();
    }
}
