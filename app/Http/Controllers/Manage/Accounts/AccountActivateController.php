<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\Account;

class AccountActivateController extends WebController
{
    use AuthorizesPermissions;

    public function update(Account $account)
    {
        $this->can(WebManagePermission::ACCOUNTS_EDIT);

        $account->activated = true;
        $account->disableLogging()->save();

        activity()->on($account)
            ->log('manually activated');

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account activated']);
    }

    public function destroy(Account $account)
    {
        $this->can(WebManagePermission::ACCOUNTS_EDIT);

        $account->activated = false;
        $account->disableLogging()->save();

        activity()->on($account)
            ->log('manually deactivated');

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account deactivated']);
    }
}
