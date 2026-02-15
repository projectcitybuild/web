<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\EmailChange\UseCases\UpdateAccountEmail;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\EmailChange;

class AccountApproveEmailChangeController extends WebController
{
    use AuthorizesPermissions;

    public function __invoke(
        Account $account,
        EmailChange $accountEmailChange,
        UpdateAccountEmail $updateAccountEmail,
    ) {
        $this->can(WebManagePermission::ACCOUNTS_EDIT);

        if (! $accountEmailChange->account->is($account)) {
            abort(422);
        }

        $updateAccountEmail->execute(
            account: $account,
            emailChangeRequest: $accountEmailChange,
            oldEmail: $account->email,
        );

        return redirect()->back()
            ->with(['success' => 'Email change force approved']);
    }
}
