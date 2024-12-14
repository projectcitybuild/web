<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\MinecraftEventBus\Events\MinecraftPlayerUpdated;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccountUpdateBadges
{
    public function __invoke(
        Request $request,
        Account $account,
    ) {
        Gate::authorize('update', $account);

        $account->badges()->sync($request->badges);

        $account->minecraftAccount()->each(function ($player) {
            MinecraftPlayerUpdated::dispatch($player);
        });

        return redirect()->back();
    }
}
