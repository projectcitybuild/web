<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\WebController;
use App\Models\Account;

class AccountActivate extends WebController
{
    public function __invoke(Account $account)
    {
        $account->activated = true;
        $account->disableLogging()->save();
        activity()->on($account)
            ->log('manually activated');

        return redirect()->back();
    }
}
