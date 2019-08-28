<?php


namespace App\Http\Controllers\Panel;


use App\Entities\Accounts\Models\Account;
use App\Entities\Players\Models\MinecraftPlayer;

class AccountGameAccount
{
    public function delete(Account $account, MinecraftPlayer $minecraftPlayer)
    {
        if (!$minecraftPlayer->account->is($account)) {
            return abort(422);
        }

        $minecraftPlayer->aliases()->delete();
        $minecraftPlayer->delete();

        return redirect()->back();
    }
}
