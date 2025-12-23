<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Activation\UseCases\SendActivationEmail;
use App\Models\Account;
use Illuminate\Support\Facades\Gate;

class AccountResendActivationController
{
    public function __invoke(Account $account, SendActivationEmail $sendActivationEmail)
    {
        Gate::authorize('update', $account);

        $sendActivationEmail->execute($account);

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Activation email sent to '.$account->email]);
    }
}
