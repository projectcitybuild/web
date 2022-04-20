<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Entities\Models\Eloquent\Account;

class AccountActivate extends WebController
{
    public function __invoke(Account $account)
    {
        $account->activated = true;
        $account->save();

        return redirect()->back();
    }
}
