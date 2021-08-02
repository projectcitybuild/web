<?php

namespace Domain\Payments;

use App\Entities\Payments\Models\AccountPaymentSession;
use App\Library\Stripe\StripeWebhook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class DonationService
{
    private PaymentAdapter $paymentAdapter;

    public function __constructor(PaymentAdapter $paymentAdapter)
    {
        $this->paymentAdapter = $paymentAdapter;
    }

    public function startCheckout(string $productId, ?int $accountId): string
    {
        $pcbSessionUUID = Str::uuid();
        $checkoutURL = $this->paymentAdapter->createCheckoutSession($pcbSessionUUID, $productId);

        $pcbPaymentSession = AccountPaymentSession::create([
            'session_id' => $pcbSessionUUID->toString(),
            'account_id' => Auth::id(),
            'is_processed' => false,
        ]);

        Log::debug('Generated payment session', ['session' => $pcbPaymentSession]);

        return $checkoutURL;
    }

    // FIXME!!!
    public function processDonation(string $sessionId, string $transactionId, int $amountPaidInCents)
    {
//        // Sanity check
//        if ($amountPaidInCents <= 0) {
//            throw new \Exception('Received a zero amount donation from Stripe');
//        }
//
//        $session = AccountPaymentSession::where('session_id', $sessionId)->first();
//        if ($session === null) {
//            throw new \Exception('Could not fulfill donation. Internal session id not found: '.$sessionId);
//        }
//        Log::debug('Found associated session', ['session' => $session]);
//
//        $accountId = $session->account !== null ? $session->account->getKey() : null;
//        $amountInDollars = (float) ($amountPaidInCents / 100);
//
//        $numberOfMonthsOfPerks = 0;
//        $donationExpiry = null;
//        $isLifetime = $amountInDollars >= Donation::LIFETIME_REQUIRED_AMOUNT;
//
//        if (! $isLifetime) {
//            $numberOfMonthsOfPerks = floor($amountInDollars / Donation::ONE_MONTH_REQUIRED_AMOUNT);
//            $donationExpiry = now()->addMonths($numberOfMonthsOfPerks);
//        }
//
//        $donation = null;
//        DB::beginTransaction();
//        try {
//            $donation = Donation::create([
//                'account_id' => $accountId,
//                'amount' => $amountInDollars,
//            ]);
//
//            if ($accountId !== null) {
//                DonationPerk::create([
//                    'donation_id' => $donation->getKey(),
//                    'account_id' => $accountId,
//                    'is_active' => true,
//                    'is_lifetime_perks' => $isLifetime,
//                    'expires_at' => $donationExpiry,
//                ]);
//            }
//
//            AccountPayment::create([
//                'payment_type' => AccountPaymentType::Donation,
//                'payment_id' => $donation->getKey(),
//                'payment_amount' => $amountPaidInCents,
//                'payment_source' => $transactionId,
//                'account_id' => $accountId,
//                'is_processed' => true,
//                'is_refunded' => false,
//                'is_subscription_payment' => false,
//            ]);
//
//            $session->is_processed = true;
//            $session->save();
//
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollBack();
//            throw $e;
//        }
//
//        // Add user to Donator group if they're logged in
//        if ($session->account !== null && ($numberOfMonthsOfPerks > 0 || $isLifetime)) {
//            Log::debug('Adding donator perks to account');
//
//            $donatorGroup = Group::where('name', 'donator')->first();
//            $donatorGroupId = $donatorGroup->getKey();
//
//            if (! $session->account->groups->contains($donatorGroupId)) {
//                $session->account->groups()->attach($donatorGroupId);
//            }
//
//            // Detach the user from the member group
//            $memberGroup = Group::where('is_default', true)->first();
//            $memberGroupId = $memberGroup->getKey();
//            $session->account->groups()->detach($memberGroupId);
//        }
    }
}
