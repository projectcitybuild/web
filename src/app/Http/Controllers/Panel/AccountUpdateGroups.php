<?php


namespace App\Http\Controllers\Panel;



use App\Entities\Accounts\Models\Account;
use Illuminate\Http\Request;

class AccountUpdateGroups
{
    public function __invoke(Request $request, Account $account)
    {
        // TODO: consider ID validation
        $account->groups()->sync($request->groups);

        return redirect()->back();
    }
}
