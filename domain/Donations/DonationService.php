<?php

namespace Domain\Donations;

use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPaymentSession;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Models\DonationTier;
use App\Entities\Groups\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class DonationService
{
    private PaymentAdapter $paymentAdapter;

    public function __construct(PaymentAdapter $paymentAdapter)
    {
        $this->paymentAdapter = $paymentAdapter;
    }

    public function startCheckoutSession(string $productId, int $numberOfMonthsToBuy, bool $isSubscription): string
    {
        $pcbSessionUUID = Str::uuid();
        $checkoutURL = $this->paymentAdapter->createCheckoutSession(
            $pcbSessionUUID,
            $productId,
            $numberOfMonthsToBuy,
            $isSubscription
        );

        $donationTier = DonationTier::where('stripe_payment_price_id', $productId)
            ->orWhere('stripe_subscription_price_id', $productId)
            ->first();

        if ($donationTier === null) {
            throw new \Exception('No donation tier found for product id '.$productId);
        }

        $donationSession = DonationPaymentSession::create([
            'account_id' => Auth::id(),
            'donation_tier_id' => $donationTier->getKey(),
            'donation_perks_id' => null,
            'session_id' => $pcbSessionUUID->toString(),
            'stripe_transaction_id' => null,
            'stripe_price_id' => $productId,
            'number_of_months' => $numberOfMonthsToBuy,
            'is_processed' => false,
            'is_refunded' => false,
            'is_subscription' => $isSubscription,
        ]);

        Log::debug('Generated payment session', ['session' => $donationSession]);

        return $checkoutURL;
    }

    public function processDonation(string $sessionId, string $transactionId, int $amountPaidInCents)
    {
        Log::info('Processing donation...', [
            'session_id' => $sessionId,
            'transaction_id' => $transactionId,
        ]);

        // Sanity check
        if ($amountPaidInCents <= 0) {
            // Something's gone seriously wrong if this happens
            throw new \Exception('Received a zero amount donation from Stripe');
        }

        $session = DonationPaymentSession::where('session_id', $sessionId)->first();
        if ($session === null) {
            throw new \Exception('Could not fulfill donation. AccountPaymentSession id not found: '.$sessionId);
        }
        Log::debug('Found associated session', ['session' => $session]);

        $accountId = $session->account !== null ? $session->account->getKey() : null;
        $amountPaidInDollars = (float) ($amountPaidInCents / 100);
        $perksExpiryDate = now()->addMonths($session->number_of_months);

        $donation = null;

        DB::beginTransaction();
        try {
            $donation = Donation::create([
                'account_id' => $accountId,
                'amount' => $amountPaidInDollars,
            ]);

            if ($accountId !== null) {
                $donationPerk = DonationPerk::create([
                    'donation_id' => $donation->getKey(),
                    'donation_tier_id' => $session->donation_tier_id,
                    'account_id' => $accountId,
                    'is_active' => true,
                    'expires_at' => $perksExpiryDate,
                ]);
                $session->donation_perks_id = $donationPerk->getKey();
            }
            $session->stripe_transaction_id = $transactionId;
            $session->is_processed = true;
            $session->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        // Add user to Donor group if possible
        if ($session->account !== null && $session->number_of_months > 0) {
            $donatorGroup = Group::where('name', Group::DONOR_GROUP_NAME)->first();
            $donatorGroupId = $donatorGroup->getKey();

            if (! $session->account->groups->contains($donatorGroupId)) {
                $session->account->groups()->attach($donatorGroupId);
            }

            // Detach the user from the member group
            $memberGroup = Group::where('is_default', true)->first();
            $memberGroupId = $memberGroup->getKey();
            $session->account->groups()->detach($memberGroupId);
        }
    }
}
