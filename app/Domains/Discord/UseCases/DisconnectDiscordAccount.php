<?php

namespace App\Domains\Discord\UseCases;

use App\Core\Domains\Auditing\AuditEventRecorder;
use App\Core\Domains\Discord\DiscordRoleSyncDispatcher;
use App\Models\Account;
use App\Models\ExternalAccount;
use Illuminate\Support\Facades\DB;

final class DisconnectDiscordAccount
{
    public function __construct(
        private AuditEventRecorder $auditEventRecorder,
        private DiscordRoleSyncDispatcher $dispatcher,
    ) {}

    public function execute(Account $account): ?string
    {
        return DB::transaction(function () use ($account): ?string {
            $externalAccount = $account->externalAccounts()
                ->where('provider', ExternalAccount::PROVIDER_DISCORD)
                ->lockForUpdate()
                ->first();

            if ($externalAccount === null) {
                return null;
            }

            $discordUserId = $externalAccount->provider_user_id;
            $username = $externalAccount->username;
            $externalAccount->delete();

            $this->auditEventRecorder->record(
                subject: $account,
                event: 'disconnected Discord account',
                oldValues: [
                    'discord_user_id' => $discordUserId,
                    'discord_username' => $username,
                ],
            );
            $this->dispatcher->dispatchForDiscordUserId($discordUserId, 'disconnected');

            return $discordUserId;
        });
    }
}
