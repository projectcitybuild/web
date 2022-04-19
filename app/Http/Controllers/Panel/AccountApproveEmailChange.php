<?php

namespace App\Http\Controllers\Panel;

use App\Http\Actions\AccountSettings\UpdateAccountEmail;
use App\Http\WebController;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountEmailChange;

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
