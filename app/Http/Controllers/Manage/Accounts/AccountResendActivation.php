<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Activation\Notifications\AccountNeedsActivationNotification;
use App\Models\Account;
use Illuminate\Support\Facades\Gate;

class AccountResendActivation
{
    public function __invoke(Account $account)
    {
        Gate::authorize('update', $account);

        $account->notify(new AccountNeedsActivationNotification($account));

        return redirect()->back();
    }
}
