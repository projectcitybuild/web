<?php

namespace App\Http\Controllers\Panel;

use App\Domains\MinecraftEventBus\Events\MinecraftPlayerUpdated;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountUpdateGroups
{
    public function __invoke(
        Request $request,
        Account $account,
    ) {
        // TODO: consider ID validation
        $account->groups()->sync($request->groups);

        $account->minecraftAccount()->each(function ($player) {
            MinecraftPlayerUpdated::dispatch($player);
        });

        return redirect()->back();
    }
}
