<?php

namespace Domain\Donations;

use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Models\DonationTier;
use App\Entities\Groups\Models\Group;
use App\Entities\Payments\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Billable;
use Stripe\StripeClient;

final class DonationService
{
    public function processPayment(
        Billable $account,
        string $productId,
        string $priceId,
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

        $donationTier = DonationTier::where('stripe_product_id', $productId)->first();
        if ($donationTier === null) {
            throw new \Exception('No donation tier found for product id: ' . $productId);
        }

        $existingPerk = DonationPerk::where('account_id', $account->getKey())
            ->where('stripe_product_id', $productId)
            ->where('is_active', true)
            ->first();

        DB::beginTransaction();
        try {
            $amountPaidInDollars = (float)($amountPaidInCents / 100);
            $donation = Donation::create([
                'account_id' => $account->getKey(),
                'amount' => $amountPaidInDollars,
            ]);

            if ($existingPerk === null) {
                DonationPerk::create([
                    'donation_id' => $donation->getKey(),
                    'donation_tier_id' => $donationTier->getKey(),
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

            $donatorGroup = Group::where('name', Group::DONOR_GROUP_NAME)->first();
            $donatorGroupId = $donatorGroup->getKey();

            if (!$account->groups->contains($donatorGroupId)) {
                $account->groups()->attach($donatorGroupId);
            }

            // Detach the user from the member group
            $memberGroup = Group::where('is_default', true)->first();
            $memberGroupId = $memberGroup->getKey();
            $account->groups()->detach($memberGroupId);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
