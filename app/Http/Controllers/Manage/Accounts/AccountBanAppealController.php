<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\EmailChange\UseCases\UpdateAccountEmail;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\EmailChange;
use Illuminate\Support\Facades\Gate;

class AccountBanAppealController extends WebController
{
    public function index(Account $account) {
        Gate::authorize('view', $account);

        $appeals = $account->banAppeals()->paginate(100);

        return $appeals;
    }
}
