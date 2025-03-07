<?php

namespace App\Domains\Donations\UseCases;

use App\Core\Data\Exceptions\BadRequestException;
use App\Core\Domains\Payment\Data\Amount;
use App\Domains\Donations\Exceptions\StripeProductNotFoundException;
use App\Domains\Donations\Notifications\DonationPerkStartedNotification;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\Group;
use App\Models\StripeProduct;
use Illuminate\Support\Carbon;

final class ProcessDonation
{
    /**
     * @throws StripeProductNotFoundException if productId does not exist in the StripeProducts table
     * @throws BadRequestException if quantity or paidAmount is invalid
     */
    public function execute(
        Account $account,
        string $productId,
        string $priceId,
        Amount $paidAmount,
        Amount $originalAmount,
        int $quantity,
    ) {
        abort_if(
            $paidAmount->value <= 0 || $originalAmount->value <= 0,
            code: 400,
            message: 'Amount paid was zero',
        );
        abort_if(
            $quantity <= 0,
            code: 400,
            message: 'Quantity purchased was zero',
        );

        $donorGroup = Group::whereDonor()->first();
        throw_if($donorGroup === null);

        $product = StripeProduct::where('product_id', $productId)
            ->where('price_id', $priceId)
            ->first()
            ?? throw new StripeProductNotFoundException();

        $donation = Donation::create([
            'account_id' => $account->getKey(),
            'amount' => $originalAmount->value,
        ]);

        $donationTier = $product->donationTier;
        if ($donationTier !== null) {
            $existingPerk = DonationPerk::where('account_id', $account->getKey())
                ->where('donation_tier_id', $donationTier->getKey())
                ->where('is_active', true)
                ->whereNotNull('expires_at')
                ->orderBy('expires_at', 'desc')
                ->first();

            $expiryDate = $this->calculateExpiryDate(
                numberOfMonths: $quantity,
                existingPerk: $existingPerk,
            );

            // TODO: update existing instead

            $newPerk = DonationPerk::create([
                'donation_id' => $donation->getKey(),
                'donation_tier_id' => $donationTier?->getKey(),
                'account_id' => $account->getKey(),
                'is_active' => true,
                'expires_at' => $expiryDate,
            ]);

            $account->groups()->syncWithoutDetaching([$donorGroup->getKey()]);

            // TODO: only notify if didn't exist before
            $notification = new DonationPerkStartedNotification($expiryDate);
            $account->notify($notification);
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
        $monthsFromNow = now()->addMonths($numberOfMonths);

        if ($existingPerk === null) {
            return $monthsFromNow;
        }

        $monthsFromLastExpiry = $existingPerk
            ->expires_at
            ->copy()
            ->addMonths($numberOfMonths);

        return $monthsFromLastExpiry->gt($monthsFromNow)
            ? $monthsFromLastExpiry
            : $monthsFromNow;
    }
}
