<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Mfa\Notifications\MfaDisabledNotification;
use App\Http\Controllers\WebController;
use App\Models\Account;
use Illuminate\Support\Facades\Gate;

class AccountMfaController extends WebController
{
    public function destroy(Account $account)
    {
        Gate::authorize('update', $account);

        $account->resetTotp();
        $account->save();
        $account->notify(new MfaDisabledNotification());

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Account 2FA deactivated']);
    }
}
