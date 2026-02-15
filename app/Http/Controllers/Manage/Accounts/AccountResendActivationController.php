<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Activation\UseCases\SendActivationEmail;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;

class AccountResendActivationController
{
    use AuthorizesPermissions;

    public function __invoke(Account $account, SendActivationEmail $sendActivationEmail)
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        $sendActivationEmail->execute($account);

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Activation email sent to '.$account->email]);
    }
}
