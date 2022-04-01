<?php

namespace Domain\Donations\Repositories;

use App\Entities\Models\Eloquent\DonationPerk;
use Illuminate\Support\Carbon;

final class DonationPerkRepository
{
    public function first(
        int $accountId,
        int $donationTierId,
    ): ?DonationPerk {
        return DonationPerk::where('account_id', $accountId)
            ->where('donation_tier_id', $donationTierId)
            ->where('is_active', true)
            ->first();
    }

    public function create(
        int $donationId,
        int $donationTierId,
        int $accountId,
        Carbon $expiresAt,
    ) : DonationPerk {
        return DonationPerk::create([
            'donation_id' => $donationId,
            'donation_tier_id' => $donationTierId,
            'account_id' => $accountId,
            'is_active' => true,
            'expires_at' => $expiresAt,
        ]);
    }
}
