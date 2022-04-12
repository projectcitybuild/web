<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\AccountLink;

final class AccountLinkRepository
{
    public function create(
        int $accountId,
        string $providerName,
        string $providerId,
        string $providerEmail
    ): AccountLink {
        return AccountLink::create([
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
        return AccountLink::where('account_id', $accountId)
            ->update([
                'provider_name' => $providerName,
                'provider_id' => $providerId,
                'provider_email' => $providerEmail,
            ]);
    }
}
