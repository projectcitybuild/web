<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\MinecraftPlayer;

class AccountGameAccountController
{
    use AuthorizesPermissions;

    public function delete(Account $account, MinecraftPlayer $minecraftPlayer)
    {
        $this->can(WebManagePermission::ACCOUNTS_EDIT);

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
