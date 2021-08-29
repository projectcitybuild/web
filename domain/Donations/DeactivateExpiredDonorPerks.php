<?php

namespace Domain\Donations;

use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Notifications\DonationEndedNotification;
use Illuminate\Support\Facades\Log;

final class DeactivateExpiredDonorPerks
{
    private DonationGroupSyncService $groupSyncService;

    public function __construct(DonationGroupSyncService $groupSyncService)
    {
        $this->groupSyncService = $groupSyncService;
    }

    public function execute()
    {
        $expiredPerks = DonationPerk::where('is_active', true)
            ->whereDate('expires_at', '<=', now())
            ->get();

        if ($expiredPerks === null || count($expiredPerks) === 0) {
            Log::info('No donation perks have expired');

            return;
        }

        foreach ($expiredPerks as $expiredPerk) {
            $account = $expiredPerk->account;

            if ($account !== null) {
                // Check that the user doesn't have any other active perks
                $activePerkCount = DonationPerk::where('account_id', $account->getKey())
                    ->where('is_active', true)
                    ->where('expires_at', '>', now())
                    ->count();

                if ($activePerkCount === 0) {
                    $this->groupSyncService->removeFromDonorGroup($account);

                    $account->notify(new DonationEndedNotification());
                }
            }

            $expiredPerk->is_active = false;
            $expiredPerk->save();

            Log::info('Perks ('.$expiredPerk->getKey().') for donation_id '.$expiredPerk->donation_id.' has expired');
        }

        Log::info(count($expiredPerks).' donation perks have ended');
    }
}
