<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Domain\EmailChange\UseCases\UpdateAccountEmailUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountEmailChange;

class AccountApproveEmailChange extends WebController
{
    public function __invoke(Account $account, AccountEmailChange $accountEmailChange, UpdateAccountEmailUseCase $updateAccountEmail)
    {
        if (! $accountEmailChange->account->is($account)) {
            abort(422);
        }

        $updateAccountEmail->execute($account, $accountEmailChange);

        return redirect()->back();
    }
}
