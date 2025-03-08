<?php

namespace App\Domains\Donations\UseCases;

use App\Domains\Donations\Notifications\DonationPerkStartedNotification;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\Group;
use App\Models\StripeProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ProcessDonation
{
    public function execute(
        ?Account $account,
        string $productId,
        string $priceId,
        int $paymentId,
        int $unitAmount,
        int $unitQuantity,
    ) {
        abort_if($unitAmount <= 0, code: 400, message: 'Amount paid was zero');
        abort_if($unitQuantity <= 0, code: 400, message: 'Quantity purchased was zero');

        DB::transaction(function () use ($account, $productId, $priceId, $paymentId, $unitAmount, $unitQuantity) {
            $donation = Donation::create([
                'account_id' => $account?->getKey(),
                'amount' => $unitAmount * $unitQuantity,
                'payment_id' => $paymentId,
            ]);

            $product = StripeProduct::where('product_id', $productId)
                ->where('price_id', $priceId)
                ->first();
            throw_if($product === null, 'StripeProduct ['.$productId.'] not found');

            if ($account === null) {
                Log::warning('Donation had no associated account', compact('donation'));
                return;
            }
            $this->fulfillDonation(
                account: $account,
                donation: $donation,
                product: $product,
                numberOfMonths: $unitQuantity,
            );
        });
    }

    private function fulfillDonation(
        Account $account,
        Donation $donation,
        StripeProduct $product,
        int $numberOfMonths,
    ) {
        $donorGroup = Group::getDonorOrThrow();
        $account->groups()->syncWithoutDetaching([$donorGroup->getKey()]);

        $donationTier = $product->donationTier;
        if ($donationTier === null) {
            return;
        }
        $existingPerk = DonationPerk::where('account_id', $account->getKey())
            ->where('donation_tier_id', $donationTier->getKey())
            ->where('is_active', true)
            ->whereNotNull('expires_at')
            ->orderBy('expires_at', 'desc')
            ->first();

        $expiryDate = $this->calculateExpiryDate(
            numberOfMonths: $numberOfMonths,
            existingPerk: $existingPerk,
        );

        if ($existingPerk !== null) {
            $existingPerk->expires_at = $expiryDate;
            $existingPerk->save();
        } else {
            DonationPerk::create([
                'donation_id' => $donation->getKey(),
                'donation_tier_id' => $donationTier?->getKey(),
                'account_id' => $account->getKey(),
                'is_active' => true,
                'expires_at' => $expiryDate,
            ]);
            $account->notify(
                new DonationPerkStartedNotification($expiryDate),
            );
        }
    }

    /**
     * Calculates an expiry date `$numberOfMonths` into the future either from
     * now or from the given donation perk's expiry date. Whichever is furthest
     * into the future.
     *
     * This is to ensure the user gets their full duration of perks even if
     * they donated before their previous perks had expired.
     *
     * @param  int  $numberOfMonths Number of months to add to the base date
     * @param  DonationPerk|null  $existingPerk The user's latest DonationPerk
     * @return Carbon Expiry date
     */
    private function calculateExpiryDate(
        int $numberOfMonths,
        ?DonationPerk $existingPerk,
    ): Carbon {
        $now = now();
        $startDate = $existingPerk === null || $now->gte($existingPerk->expires_at)
            ? $now
            : $existingPerk->expires_at->copy();

        return $startDate->addMonths($numberOfMonths);
    }
}
