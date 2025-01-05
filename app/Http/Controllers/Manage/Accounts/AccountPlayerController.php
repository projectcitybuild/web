<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Group;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AccountPlayerController extends WebController
{
    public function create(Request $request, Account $account)
    {
        Gate::authorize('update', $account);

        return Inertia::render('Accounts/AccountPlayerSelect', [
            'account_id' => $account->getKey(),
        ]);
    }

    public function store(Request $request, Account $account)
    {
        Gate::authorize('update', $account);

        $validated = $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['required', 'string'],
        ]);

        $uuid = MinecraftUUID::tryParse($validated['uuid']);
        $player = MinecraftPlayer::firstOrCreate($uuid, alias: $validated['alias']);

        $player->account()->associate($account);
        $player->save();

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Player linked successfully.']);
    }

    public function destroy(Request $request, Account $account, MinecraftPlayer $player)
    {
        Gate::authorize('update', $account);

        $player->account()->disassociate();
        $player->save();

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Player unlinked successfully.']);
    }
}
