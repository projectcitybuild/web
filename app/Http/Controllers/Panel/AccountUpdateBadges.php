<?php

namespace App\Http\Controllers\Panel;

use Entities\Models\Eloquent\Account;
use Illuminate\Http\Request;

class AccountUpdateBadges
{
    public function __invoke(
        Request $request,
        Account $account,
    ) {
        $account->badges()->sync($request->badges);

        return redirect()->back();
    }
}
