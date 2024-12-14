<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Http\Controllers\WebController;
use App\Models\Account;
use Illuminate\Support\Facades\Gate;

class AccountActivate extends WebController
{
    public function __invoke(Account $account)
    {
        Gate::authorize('update', $account);

        $account->activated = true;
        $account->disableLogging()->save();

        activity()->on($account)
            ->log('manually activated');

        return redirect()->back();
    }
}
