<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Http\Controllers\WebController;
use App\Models\Account;
use Illuminate\Support\Facades\Gate;

class AccountActivateController extends WebController
{
    public function update(Account $account)
    {
        Gate::authorize('update', $account);

        $account->activated = true;
        $account->disableLogging()->save();

        activity()->on($account)
            ->log('manually activated');

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account activated']);
    }

    public function destroy(Account $account)
    {
        Gate::authorize('update', $account);

        $account->activated = false;
        $account->disableLogging()->save();

        activity()->on($account)
            ->log('manually deactivated');

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account deactivated']);
    }
}
