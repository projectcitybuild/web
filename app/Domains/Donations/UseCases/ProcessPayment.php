<?php

namespace App\Domains\Donations\UseCases;

use App\Core\Data\Exceptions\BadRequestException;
use App\Domains\Donations\Data\PaidAmount;
use App\Domains\Donations\Data\PaymentType;
use App\Domains\Donations\Events\DonationPerkCreated;
use App\Domains\Donations\Exceptions\StripeProductNotFoundException;
use App\Domains\Donations\Notifications\DonationPerkStartedNotification;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\Group;
use App\Models\Payment;
use App\Models\StripeProduct;
use Illuminate\Support\Carbon;

final class ProcessPayment
{
    /**
     * @throws StripeProductNotFoundException if productId does not exist in the StripeProducts table
     * @throws BadRequestException if quantity or paidAmount is invalid
     */
    public function execute(
        Account $account,
        string $productId,
        string $priceId,
        PaidAmount $paidAmount,
        int $quantity,
        PaymentType $donationType,
    ) {
        abort_if($paidAmount->toCents() <= 0, 400, 'Amount paid was zero');
        abort_if($quantity <= 0, 400, 'Quantity purchased was zero');

        $donorGroup = Group::whereDonor()->first();
        throw_if($donorGroup === null);

        $product = StripeProduct::where('product_id', $productId)
            ->where('price_id', $priceId)
            ->first()
            ?? throw new StripeProductNotFoundException();

        $donation = Donation::create([
            'account_id' => $account->getKey(),
            'amount' => $paidAmount->toDollars(),
        ]);

        Payment::create([
            'account_id' => $account->getKey(),
            'stripe_product' => $productId,
            'stripe_price' => $priceId,
            'amount_paid_in_cents' => $paidAmount->toCents(),
            'quantity' => $quantity,
            'is_subscription_payment' => $donationType == PaymentType::SUBSCRIPTION,
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

            $newPerk = DonationPerk::create([
                'donation_id' => $donation->getKey(),
                'donation_tier_id' => $donationTier?->getKey(),
                'account_id' => $account->getKey(),
                'is_active' => true,
                'expires_at' => $expiryDate,
            ]);

            $account->groups()->syncWithoutDetaching([$donorGroup->getKey()]);

            $notification = new DonationPerkStartedNotification($expiryDate);
            $account->notify($notification);

            DonationPerkCreated::dispatch($newPerk);
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
