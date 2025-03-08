<?php

namespace App\Domains\Donations\UseCases;

use App\Core\Domains\Auditing\Causers\SystemCauser;
use App\Core\Domains\Auditing\Causers\SystemCauseResolver;
use App\Domains\Donations\Notifications\DonationEndedNotification;
use App\Models\DonationPerk;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ExpireDonorPerks
{
    public function execute(): void
    {
        SystemCauseResolver::setCauser(SystemCauser::PERK_EXPIRY);

        $now = now();
        $expiredPerks = DonationPerk::where('is_active', true)
            ->whereDate('expires_at', '<=', $now)
            ->get() ?? collect();

        if ($expiredPerks->count() === 0) {
            Log::info('No expired DonationPerks found');
            return;
        }

        $donorGroup = Group::getDonorOrThrow();

        $expired = 0;
        foreach ($expiredPerks as $expiredPerk) {
            // Grant a grace period in case the payment is slightly delayed
            $bufferedExpiry = $expiredPerk->expires_at->copy()->addHours(12);
            if ($bufferedExpiry->gt($now)) {
                continue;
            }
            $account = $expiredPerk->account;

            // Remove from Donor group if account has no other active DonationPerk
            $hasOtherActivePerk = DonationPerk::where(DonationPerk::primaryKey(), '!=', $expiredPerk->getKey())
                ->where('account_id', $account->getKey())
                ->where('is_active', true)
                ->whereRaw('DATE_ADD(expires_at, INTERVAL 12 HOUR) <= ?', [$now])
                ->exists();

            if (! $hasOtherActivePerk) {
                DB::transaction(function () use (&$account, $donorGroup, &$expiredPerk) {
                    $account->groups()->detach($donorGroup->getKey());
                    $expiredPerk->is_active = false;
                    $expiredPerk->save();
                });
                $account->notify(new DonationEndedNotification());

                Log::info('Deactivated DonationPerk', compact('expiredPerk'));
                $expired++;
            }
        }

        Log::info($expired.' DonationPerks expired');
    }
}
