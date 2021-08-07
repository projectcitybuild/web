<?php

namespace Domain\Donations;

use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Models\DonationTier;
use App\Entities\Groups\Models\Group;
use App\Entities\Payments\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Stripe\StripeClient;

final class DonationService
{
    private StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function processPayment(Billable $account, string $sessionId)
    {
        $lineItems = $this->stripeClient->checkout->sessions
            ->allLineItems($sessionId, ['limit' => 1]);

        Log::info('Retrieved line items', ['line_items' => $lineItems]);

        $firstLine = $lineItems['data'][0];
        $price = $firstLine['price'];
        $productId = $price['product'];
        $amountPaidInCents = $firstLine['amount_total'];
        $quantity = $firstLine['quantity'];
        $isSubscriptionPayment = $price['type'] === 'recurring';

        Payment::create([
            'account_id' => $account !== null ? $account->getKey() : null,
            'stripe_price' => $price['id'],
            'stripe_product' => $productId,
            'amount_paid_in_cents' => $amountPaidInCents,
            'quantity' => $quantity,
            'is_subscription_payment' => $isSubscriptionPayment,
        ]);

        // Sanity check
        if ($amountPaidInCents <= 0) {
            throw new \Exception('Amount paid was zero');
        }
        if ($quantity <= 0) {
            throw new \Exception('Quantity purchased was zero');
        }

        $donationTier = DonationTier::where('stripe_product_id', $productId)->first();
        if ($donationTier === null) {
            throw new \Exception('No donation tier found for product id: '.$productId);
        }

        $amountPaidInDollars = (float) ($amountPaidInCents / 100);
        $perksExpiryDate = now()->addMonths($quantity);

        DB::beginTransaction();
        try {
            $donation = Donation::create([
                'account_id' => $account->getKey(),
                'amount' => $amountPaidInDollars,
            ]);

            if ($user !== null) {
                DonationPerk::create([
                    'donation_id' => $donation->getKey(),
                    'donation_tier_id' => $donationTier->getKey(),
                    'account_id' => $user->getKey(),
                    'is_active' => true,
                    'expires_at' => $perksExpiryDate,
                ]);
            }

            // Add user to Donor group if possible
            if ($user !== null) {
                $donatorGroup = Group::where('name', Group::DONOR_GROUP_NAME)->first();
                $donatorGroupId = $donatorGroup->getKey();

                if (! $user->groups->contains($donatorGroupId)) {
                    $user->groups()->attach($donatorGroupId);
                }

                // Detach the user from the member group
                $memberGroup = Group::where('is_default', true)->first();
                $memberGroupId = $memberGroup->getKey();
                $user->groups()->detach($memberGroupId);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
