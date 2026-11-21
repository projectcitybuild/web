<?php

namespace App\Http\Controllers\Admin\Manage\Accounts;

use App\Core\Domains\Discord\DiscordRoleSyncDispatcher;
use App\Domains\Discord\UseCases\DisconnectDiscordAccount;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\WebController;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;

final class AccountDiscordController extends WebController
{
    use AuthorizesPermissions;

    public function destroy(Account $account, DisconnectDiscordAccount $disconnectDiscordAccount): RedirectResponse
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        $discordUserId = $disconnectDiscordAccount->execute($account);

        return to_route('manage.accounts.show', $account)
            ->with('success', $discordUserId === null
                ? 'No Discord account was linked.'
                : 'Discord account disconnected successfully.');
    }

    public function sync(Account $account, DiscordRoleSyncDispatcher $dispatcher): RedirectResponse
    {
        $this->requires(WebManagePermission::ACCOUNTS_EDIT);

        $discordUserId = $account->discordAccount()->value('provider_user_id');

        if (! is_string($discordUserId)) {
            return to_route('manage.accounts.show', $account)
                ->with('error', 'Link a Discord account before requesting a role sync.');
        }

        $dispatcher->dispatchForDiscordUserId($discordUserId);

        return to_route('manage.accounts.show', $account)
            ->with('success', 'A Discord role sync has been queued.');
    }
}
