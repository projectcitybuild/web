<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Http\Controllers\WebController;
use App\Models\Account;
use Illuminate\Support\Facades\Gate;

class AccountBanAppealController extends WebController
{
    public function index(Account $account)
    {
        Gate::authorize('view', $account);

        $appeals = $account->banAppeals()->paginate(100);

        return $appeals;
    }
}
