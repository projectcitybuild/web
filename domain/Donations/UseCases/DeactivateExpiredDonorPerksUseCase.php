<?php

namespace Domain\Donations\UseCases;

use Entities\Models\Eloquent\Group;
use Entities\Notifications\DonationEndedNotification;
use Illuminate\Support\Facades\Log;
use Repositories\DonationPerkRepository;
use Shared\Groups\GroupsManager;

final class DeactivateExpiredDonorPerksUseCase
{
    public function __construct(
        private GroupsManager $groupsManager,
        private DonationPerkRepository $donationPerkRepository,
        private Group $donorGroup,
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
                    $this->groupsManager->removeMember(
                        group: $this->donorGroup,
                        account: $account,
                    );
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
