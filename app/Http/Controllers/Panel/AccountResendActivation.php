<?php

namespace App\Http\Controllers\Panel;

use App\Domains\SignUp\Notifications\AccountActivationNotification;
use App\Models\Account;

class AccountResendActivation
{
    public function __invoke(Account $account)
    {
        $account->notify(new AccountActivationNotification($account));

        return redirect()->back();
    }
}
