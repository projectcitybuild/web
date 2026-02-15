<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Mfa\Notifications\MfaDisabledNotification;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\Account;

class AccountMfaController extends WebController
{
    use AuthorizesPermissions;

    public function destroy(Account $account)
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        $account->resetTotp();
        $account->save();
        $account->notify(new MfaDisabledNotification);

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account 2FA deactivated']);
    }
}
