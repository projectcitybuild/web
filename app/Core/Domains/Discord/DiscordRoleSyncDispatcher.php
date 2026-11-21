<?php

namespace App\Core\Domains\Discord;

use App\Core\Domains\Discord\Jobs\SyncDiscordRoleJob;
use App\Models\Account;
use App\Models\ExternalAccount;
use Illuminate\Support\Collection;

final class DiscordRoleSyncDispatcher
{
    public function dispatchForAccount(Account $account): void
    {
        $this->dispatchForAccountId((int) $account->getKey());
    }

    public function dispatchForAccountId(int $accountId): void
    {
        $discordUserId = ExternalAccount::query()
            ->where('account_id', $accountId)
            ->where('provider', ExternalAccount::PROVIDER_DISCORD)
            ->value('provider_user_id');

        if (is_string($discordUserId)) {
            $this->dispatchForDiscordUserId($discordUserId);
        }
    }

    public function dispatchForDiscordUserId(
        string $discordUserId,
        ?string $accountEvent = null,
    ): void {
        SyncDiscordRoleJob::dispatch($discordUserId, $accountEvent)->afterCommit();
    }

    public function dispatchForAllLinkedAccounts(): void
    {
        ExternalAccount::query()
            ->where('provider', ExternalAccount::PROVIDER_DISCORD)
            ->select(['id', 'provider_user_id'])
            ->chunkById(100, function (Collection $externalAccounts): void {
                foreach ($externalAccounts as $externalAccount) {
                    $this->dispatchForDiscordUserId($externalAccount->provider_user_id);
                }
            });
    }
}
