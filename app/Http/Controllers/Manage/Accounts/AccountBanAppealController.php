<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\Account;

class AccountBanAppealController extends WebController
{
    use AuthorizesPermissions;

    public function index(Account $account)
    {
        $this->requires(WebManagePermission::ACCOUNTS_VIEW);

        $appeals = $account->banAppeals()->paginate(100);

        return $appeals;
    }
}
