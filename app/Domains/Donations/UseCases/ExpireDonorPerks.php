<?php

namespace App\Domains\Donations\UseCases;

use App\Core\Domains\Auditing\Causers\SystemCauser;
use App\Core\Domains\Auditing\Causers\SystemCauseResolver;
use App\Domains\Donations\Notifications\DonationEndedNotification;
use App\Models\DonationPerk;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ExpireDonorPerks
{
    public function execute(): void
    {
        SystemCauseResolver::setCauser(SystemCauser::PERK_EXPIRY);

        DB::transaction(function () {
            $now = now();
            $expiredPerks = DonationPerk::where('is_active', true)
                ->whereDate('expires_at', '<=', $now)
                ->get() ?? collect();

            if ($expiredPerks->count() === 0) {
                Log::debug('No expired DonationPerks found');
                return;
            }

            $donorRole = Role::getDonorOrThrow();
            $affectedAccounts = [];

            foreach ($expiredPerks as $expiredPerk) {
                // Grant a grace period in case the payment is slightly delayed
                $bufferedExpiry = $expiredPerk->expires_at->copy()->addHours(12);
                if ($bufferedExpiry->gt($now)) {
                    continue;
                }
                $account = $expiredPerk->account;
                if (! array_key_exists($account->id, $affectedAccounts)) {
                    $affectedAccounts[$account->id] = $account;
                }
                $expiredPerk->is_active = false;
                $expiredPerk->save();

                Log::info('Expired perk', compact('expiredPerk'));
            }

            // TODO: this should really be in its own queued job for better failure tolerance
            foreach ($affectedAccounts as $account) {
                // Only remove accounts from the donor role and notify them if they don't
                // have any other existing perks
                $perks = DonationPerk::where('account_id', $account->id)
                    ->where('is_active', true)
                    ->whereDate('expires_at', '>', $now)
                    ->get();

                // Also apply grace period here
                $exists = $perks->first(fn ($perk) => $perk->expires_at->copy()->addHours(12)->gt($now));
                if ($exists) {
                    continue;
                }

                $account->roles()->detach($donorRole->id);
                $account->notify(new DonationEndedNotification);

                Log::info('Removed '.$account->id.' from donators');
            }
        });
    }
}
