<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Domains\MinecraftEventBus\Events\MinecraftPlayerUpdated;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Models\Account;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccountBadgeController
{
    use RendersManageApp;

    public function index(Request $request, Account $account)
    {
        Gate::authorize('viewAny', Account::class);

        $badges = Badge::get();
        $accountBadgeIds = $account->badges->pluck(Badge::primaryKey());

        return $this->inertiaRender('Accounts/AccountBadgeSelect', [
            'badges' => $badges,
            'account_badge_ids' => $accountBadgeIds ?? [],
            'account_id' => $account->getKey(),
        ]);
    }

    public function update(Request $request, Account $account)
    {
        Gate::authorize('update', $account);

        $request->validate([
            'account_badge_ids' => [],
        ]);

        $account->badges()->sync(
            $request->get('account_badge_ids', []),
        );
        $account->minecraftAccount()->each(function ($player) {
            MinecraftPlayerUpdated::dispatch($player);
        });

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Badges updated successfully.']);
    }
}
