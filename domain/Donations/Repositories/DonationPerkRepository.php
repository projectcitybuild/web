<?php

namespace Domain\Donations\Repositories;

use App\Entities\Models\Eloquent\DonationPerk;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @final
 */
class DonationPerkRepository
{
    public function lastToExpire(
        int $accountId,
        int $donationTierId,
    ): ?DonationPerk {
        return DonationPerk::where('account_id', $accountId)
            ->where('donation_tier_id', $donationTierId)
            ->where('is_active', true)
            ->whereNotNull('expires_at')
            ->orderBy('expires_at', 'desc')
            ->first();
    }

    public function create(
        int $donationId,
        int $donationTierId,
        int $accountId,
        Carbon $expiresAt,
    ): DonationPerk {
        return DonationPerk::create([
            'donation_id' => $donationId,
            'donation_tier_id' => $donationTierId,
            'account_id' => $accountId,
            'is_active' => true,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * @return Collection|DonationPerk[]
     */
    public function getExpired(): Collection
    {
        return DonationPerk::where('is_active', true)
            ->whereDate('expires_at', '<=', now())
            ->get() ?? collect();
    }

    public function countActive(int $accountId): int
    {
        return DonationPerk::where('account_id', $accountId)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->count();
    }
}
