<?php

namespace App\Http\Controllers\Panel;

use App\Entities\Accounts\Models\Account;
use App\Http\Actions\SyncUserToDiscourse;
use Illuminate\Http\Request;

class AccountUpdateGroups
{
    public function __invoke(Request $request, Account $account)
    {
        // TODO: consider ID validation
        $account->groups()->sync($request->groups);

        $syncAction = resolve(SyncUserToDiscourse::class);
        $syncAction->setUser($account);
        $syncAction->syncAll();

        return redirect()->back();
    }
}
