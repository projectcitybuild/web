<?php

namespace Domain\Donations;

use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Payments\Models\Payment;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Billable;

final class DonationService
{
    private DonationGroupSyncService $groupSyncService;

    public function __construct(DonationGroupSyncService $groupSyncService)
    {
        $this->groupSyncService = $groupSyncService;
    }

    public function processPayment(
        Billable $account,
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
            ->where('stripe_product_id', $productId)
            ->where('is_active', true)
            ->first();

        DB::beginTransaction();
        try {
            $amountPaidInDollars = (float) ($amountPaidInCents / 100);
            $donation = Donation::create([
                'account_id' => $account->getKey(),
                'amount' => $amountPaidInDollars,
            ]);

            if ($existingPerk === null) {
                DonationPerk::create([
                    'donation_id' => $donation->getKey(),
                    'donation_tier_id' => $donationTierId,
                    'account_id' => $account->getKey(),
                    'is_active' => true,
                    'expires_at' => now()->addMonths($quantity),
                ]);
            } else {
                $extendedFromExpiry = $existingPerk->expires_at->addMonths($quantity);
                $extendedFromNow = now()->addMonths($quantity);

                // Just in case the payment was greatly delayed
                $existingPerk->expires_at = ($extendedFromExpiry->gt($extendedFromNow))
                    ? $extendedFromExpiry
                    : $extendedFromNow;

                $existingPerk->save();
            }

            $this->groupSyncService->addToDonorGroup($account);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
