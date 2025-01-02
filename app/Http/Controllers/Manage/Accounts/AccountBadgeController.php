<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\MinecraftEventBus\Events\MinecraftPlayerUpdated;
use App\Models\Account;
use App\Models\Badge;
use Illuminate\Support\Facades\Gate;

class AccountBadgeController
{
    public function destroy(
        Account $account,
        Badge $badge,
    ) {
        Gate::authorize('update', $account);

        $account->badges()->detach($badge->getKey());

        $account->minecraftAccount()->each(function ($player) {
            MinecraftPlayerUpdated::dispatch($player);
        });

        return redirect()->back()->with([
            'success' => 'Badge removed successfully.',
        ]);
    }
}
