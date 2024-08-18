<?php

namespace App\Http\Controllers\Panel;

use Entities\Models\Eloquent\Account;
use Entities\Notifications\AccountActivationNotification;

class AccountResendActivation
{
    public function __invoke(Account $account)
    {
        $account->notify(new AccountActivationNotification($account));

        return redirect()->back();
    }
}