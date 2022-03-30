<?php

namespace App\Entities\Repositories;

use App\Entities\Models\AccountLink;
use App\Repository;

/**
 * @deprecated Use AccountLink model facade instead
 */
final class AccountLinkRepository extends Repository
{
    protected $model = AccountLink::class;

    public function create(
        int $accountId,
        string $providerName,
        string $providerId,
        string $providerEmail
    ): AccountLink {
        return $this->getModel()->create([
            'provider_name' => $providerName,
            'provider_id' => $providerId,
            'provider_email' => $providerEmail,
            'account_id' => $accountId,
        ]);
    }

    public function update(
        int $accountId,
        string $providerName,
        string $providerId,
        string $providerEmail
    ): int {
        return $this->getModel()
            ->where('account_id', $accountId)
            ->update([
                'provider_name' => $providerName,
                'provider_id' => $providerId,
                'provider_email' => $providerEmail,
            ]);
    }

    public function getByUserAndProvider(string $accountId, string $providerName): ?AccountLink
    {
        return $this->getModel()
            ->where('provider_name', $providerName)
            ->where('account_id', $accountId)
            ->first();
    }

    public function getByProviderAccount(string $providerName, string $providerId): ?AccountLink
    {
        return $this->getModel()
            ->where('provider_name', $providerName)
            ->where('provider_id', $providerId)
            ->first();
    }
}
