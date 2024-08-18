<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\AccountEmailChange;
use Domain\EmailChange\UseCases\UpdateAccountEmail;

class AccountApproveEmailChange extends WebController
{
    public function __invoke(Account $account, AccountEmailChange $accountEmailChange, UpdateAccountEmail $updateAccountEmail)
    {
        if (! $accountEmailChange->account->is($account)) {
            abort(422);
        }

        $updateAccountEmail->execute($account, $accountEmailChange);

        return redirect()->back();
    }
}
