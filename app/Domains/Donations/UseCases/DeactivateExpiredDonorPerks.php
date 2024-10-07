<?php

namespace App\Domains\Donations\UseCases;

use App\Core\Domains\Groups\GroupsManager;
use App\Domains\Donations\Notifications\DonationEndedNotification;
use App\Models\Group;
use Illuminate\Support\Facades\Log;
use Repositories\DonationPerkRepository;

final class DeactivateExpiredDonorPerks
{
    public function __construct(
        private readonly GroupsManager $groupsManager,
        private readonly DonationPerkRepository $donationPerkRepository,
        private readonly Group $donorGroup,
    ) {
    }

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

                // TODO: we probably need a buffer in case payments are slightly delayed and
                // the user didn't actually cancel...
                if ($activePerkCount === 0) {
                    $account->groups()->detach($this->donorGroup->getKey());
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
