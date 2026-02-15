<?php

namespace App\Http\Controllers\Manage\Accounts;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

class AccountPlayerController extends WebController
{
    use RendersManageApp;
    use AuthorizesPermissions;

    public function create(Request $request, Account $account)
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        return $this->inertiaRender('Accounts/AccountPlayerSelect', [
            'account_id' => $account->getKey(),
        ]);
    }

    public function store(Request $request, Account $account)
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

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
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        $player->account()->disassociate();
        $player->save();

        return to_route('manage.accounts.show', $account)
            ->with(['success' => 'Player unlinked successfully.']);
    }
}
