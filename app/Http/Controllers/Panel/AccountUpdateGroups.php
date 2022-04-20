<?php

namespace App\Http\Controllers\Panel;

use App\Http\Actions\SyncUserToDiscourse;
use Entities\Models\Eloquent\Account;
use Illuminate\Http\Request;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;

class AccountUpdateGroups
{
    public function __invoke(
        Request $request,
        Account $account,
        ExternalAccountSync $externalAccountSync,
    ) {
        // TODO: consider ID validation
        $account->groups()->sync($request->groups);

        $externalAccountSync->sync($account);

        return redirect()->back();
    }
}
