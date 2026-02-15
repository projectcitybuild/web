<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\Account;

class AccountBuilderRankApplicationsController extends WebController
{
    use AuthorizesPermissions;

    public function index(Account $account)
    {
        $this->can(WebManagePermission::ACCOUNTS_VIEW);

        return $account
            ->builderRankApplications()
            ->paginate(50);
    }
}
