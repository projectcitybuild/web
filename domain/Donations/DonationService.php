<?php

namespace Domain\Donations;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Notifications\DonationPerkStartedNotification;
use App\Entities\Payments\Models\Payment;
use Illuminate\Support\Facades\DB;

final class DonationService
{
    private DonationGroupSyncService $groupSyncService;

    public function __construct(DonationGroupSyncService $groupSyncService)
    {
        $this->groupSyncService = $groupSyncService;
    }

    public function processPayment(
        Account $account,
        string $productId,
        string $priceId,
        int $donationTierId,
        int $amountPaidInCents,
        int $quantity,
        bool $isSubscription
    ) {
        Payment::create([
            'account_id' => $account->getKey(),
            'stripe_product' => $productId,
            'stripe_price' => $priceId,
            'amount_paid_in_cents' => $amountPaidInCents,
            'quantity' => $quantity,
            'is_subscription_payment' => $isSubscription,
        ]);

        $existingPerk = DonationPerk::where('account_id', $account->getKey())
            ->where('donation_tier_id', $donationTierId)
            ->where('is_active', true)
            ->first();

        $expiryDate = null;

        DB::beginTransaction();
        try {
            $amountPaidInDollars = (float) ($amountPaidInCents / 100);
            $donation = Donation::create([
                'account_id' => $account->getKey(),
                'amount' => $amountPaidInDollars,
            ]);

            if ($existingPerk === null) {
                $expiryDate = now()->addMonths($quantity);
            } else {
                // Just in case the payment was really delayed
                $extendedFromExpiry = $existingPerk->expires_at->addMonths($quantity);
                $extendedFromNow = now()->addMonths($quantity);

                $expiryDate = ($extendedFromExpiry->gt($extendedFromNow))
                    ? $extendedFromExpiry
                    : $extendedFromNow;
            }

            DonationPerk::create([
                'donation_id' => $donation->getKey(),
                'donation_tier_id' => $donationTierId,
                'account_id' => $account->getKey(),
                'is_active' => true,
                'expires_at' => $expiryDate,
            ]);

            if (! env('IS_E2E_TEST', false)) {
                // Too hard to reliable call this in E2E testing with our current set up
                $this->groupSyncService->addToDonorGroup($account);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $notification = new DonationPerkStartedNotification($expiryDate);
        $account->notify($notification);
    }
}
