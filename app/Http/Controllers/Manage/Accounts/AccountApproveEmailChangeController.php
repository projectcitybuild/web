<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\EmailChange\UseCases\UpdateAccountEmail;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\EmailChange;
use Illuminate\Support\Facades\Gate;

class AccountApproveEmailChangeController extends WebController
{
    public function __invoke(
        Account $account,
        EmailChange $accountEmailChange,
        UpdateAccountEmail $updateAccountEmail,
    ) {
        Gate::authorize('update', $account);

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
