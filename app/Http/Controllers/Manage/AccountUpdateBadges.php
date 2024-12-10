<?php

namespace App\Http\Controllers\Manage;

use App\Domains\MinecraftEventBus\Events\MinecraftPlayerUpdated;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountUpdateBadges
{
    public function __invoke(
        Request $request,
        Account $account,
    ) {
        $account->badges()->sync($request->badges);

        $account->minecraftAccount()->each(function ($player) {
            MinecraftPlayerUpdated::dispatch($player);
        });

        return redirect()->back();
    }
}
