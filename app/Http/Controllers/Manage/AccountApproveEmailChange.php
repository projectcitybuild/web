<?php

namespace App\Http\Controllers\Manage;

use App\Domains\EmailChange\UseCases\UpdateAccountEmail;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\EmailChange;

class AccountApproveEmailChange extends WebController
{
    public function __invoke(Account $account, EmailChange $accountEmailChange, UpdateAccountEmail $updateAccountEmail)
    {
        if (! $accountEmailChange->account->is($account)) {
            abort(422);
        }

        $updateAccountEmail->execute($account, $accountEmailChange);

        return redirect()->back();
    }
}
