<?php

namespace Domain\Donations\UseCases;

use App\Entities\Notifications\DonationEndedNotification;
use Domain\Donations\DonationGroupSyncService;
use Domain\Donations\Repositories\DonationPerkRepository;
use Illuminate\Support\Facades\Log;

final class DeactivateExpiredDonorPerksUseCase
{
    public function __construct(
        private DonationGroupSyncService $groupSyncService,
        private DonationPerkRepository $donationPerkRepository,
    ) {}

    public function execute()
    {
        $expiredPerks = $this->donationPerkRepository->getExpired();

        if ($expiredPerks->count() === 0) {
            Log::info('No expired DonationPerks found');
            return;
        }

        foreach ($expiredPerks as $expiredPerk) {
            $account = $expiredPerk->account;

            // Remove from Donor group if account has no other active DonationPerk
            if ($account !== null) {
                $activePerkCount = $this->donationPerkRepository->countActive(accountId: $account->getKey());

                if ($activePerkCount === 0) {
                    $this->groupSyncService->removeFromDonorGroup($account);

                    $account->notify(new DonationEndedNotification());
                }
            }

            Log::debug('Deactivating DonationPerk', ['donation_perk' => $expiredPerk]);

            $expiredPerk->is_active = false;
            $expiredPerk->save();
        }

        Log::info('Run complete. '.$expiredPerks->count().' DonationPerks expired');
    }
}
