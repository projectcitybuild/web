<?php

namespace App\Domains\Donations\UseCases;

use App\Domains\Donations\Notifications\DonationEndedNotification;
use App\Models\DonationPerk;
use App\Models\Group;
use Illuminate\Support\Facades\Log;

final class DeactivateExpiredDonorPerks
{
    public function execute(): void
    {
        $expiredPerks = DonationPerk::where('is_active', true)
            ->whereDate('expires_at', '<=', now())
            ->get() ?? collect();

        if ($expiredPerks->count() === 0) {
            Log::info('No expired DonationPerks found');
            return;
        }

        $donorGroup = Group::where('name', Group::DONOR_GROUP_NAME)->first();
        if ($donorGroup === null) {
            throw new \Exception('Could not find donor group');
        }

        foreach ($expiredPerks as $expiredPerk) {
            $account = $expiredPerk->account;

            // Remove from Donor group if account has no other active DonationPerk
            if ($account !== null) {
                $activePerkCount = DonationPerk::where('account_id', $account->getKey())
                    ->where('is_active', true)
                    ->where('expires_at', '>', now())
                    ->count();

                // TODO: we probably need a buffer in case payments are slightly delayed and
                // the user didn't actually cancel...
                if ($activePerkCount === 0) {
                    $account->groups()->detach($donorGroup->getKey());
                    $account->notify(new DonationEndedNotification());
                }
            }

            Log::info('Deactivating DonationPerk', ['donation_perk' => $expiredPerk]);

            $expiredPerk->is_active = false;
            $expiredPerk->save();
        }

        Log::info('Run complete. '.$expiredPerks->count().' DonationPerks expired');
    }
}
