<?php

namespace App\Http\Controllers\Panel;

use App\Models\Account;
use App\Models\MinecraftPlayer;

class AccountGameAccount
{
    public function delete(Account $account, MinecraftPlayer $minecraftPlayer)
    {
        if (! $minecraftPlayer->account->is($account)) {
            abort(422);
        }

        $minecraftPlayer->account_id = null;
        $minecraftPlayer->disableLogging()->save();

        activity()
            ->on($minecraftPlayer)
            ->withProperty('old', ['account_id' => $account->getKey()])
            ->withProperty('attributes', ['account_id' => null])
            ->event('updated')
            ->log('unlinked from account');

        return redirect()->back();
    }
}
