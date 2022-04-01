<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Models\Eloquent\Account;
use App\Http\WebController;

class AccountActivate extends WebController
{
    public function __invoke(Account $account)
    {
        $account->activated = true;
        $account->save();

        return redirect()->back();
    }
}
