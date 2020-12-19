<?php


namespace App\Http\Controllers\Panel;


use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Notifications\AccountActivationNotification;

class AccountResendActivation
{
    public function __invoke(Account $account)
    {
        $account->notify(new AccountActivationNotification($account));

        return redirect()->back();
    }
}
