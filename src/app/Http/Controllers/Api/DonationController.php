<?php

namespace App\Http\Controllers\Api;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Groups\Models\Group;
use App\Entities\Payments\AccountPaymentType;
use App\Entities\Payments\Models\AccountPayment;
use App\Entities\Payments\Models\AccountPaymentSession;
use App\Http\ApiController;
use App\Library\Stripe\StripeHandler;
use App\Library\Stripe\StripeWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Stripe\Error\SignatureVerification;

final class DonationController extends ApiController
{
    /**
     * @var StripeHandler
     */
    private $stripeHandler;

    public function __construct(StripeHandler $stripeHandler)
    {
        $this->stripeHandler = $stripeHandler;
    }

    public function create(Request $request)
    {
        $pcbSessionUuid = Uuid::fromInteger(time());
        $stripeSessionId = $this->stripeHandler->createCheckoutSession($pcbSessionUuid);

        $account = Auth::user();
        $accountId = $account !== null ? $account->getKey() : null;

        AccountPaymentSession::create([
            'session_id' => $pcbSessionUuid->toString(),
            'account_id' => $accountId,
            'is_processed' => false,
        ]);

        return [
            'data' => [
                'session_id' => $stripeSessionId,
            ]
        ];
    }

    public function store(Request $request)
    {
        $endpointSecret = config('services.stripe.secret');
        $payload = $request->getContent();
        $signature = $request->headers->get('stripe-signature');

        $webhook = null;

        try {
            $webhook = $this->stripeHandler->getWebhookEvent($payload, $signature, $endpointSecret);
        }
        catch(\UnexpectedValueException $e) {
            // Invalid payload
            app('sentry')->captureException($e);
            abort(400);
        }
        catch(SignatureVerification $e) {
            // Invalid signature
            app('sentry')->captureException($e);
            abort(400);
        }

        $session = AccountPaymentSession::where('session_id', $webhook->getSessionId())->first();
        if ($session === null) {
            throw new \Exception('Could not fulfill donation. Internal session id not found: '.$webhook->getSessionId());
        }

        if ($webhook->getEvent() == StripeWebhookEvent::CheckoutSessionCompleted) {
            if ($webhook->getAmountInCents() <= 0) {
                abort(400, 'Received a zero amount donation from Stripe');
            }

            $this->fulfillDonation(
                $webhook->getTransactionId(),
                $webhook->getAmountInCents(),
                $session
            );
        }

        return response()->json(null, 200);
    }

    private function fulfillDonation(string $transactionId, int $amountInCents, AccountPaymentSession $session)
    {
        $accountId = $session->account !== null ? $session->account->getKey() : null;

        $amountInDollars = (float)($amountInCents / 100);
        $isLifetime = $amountInDollars >= Donation::LIFETIME_REQUIRED_AMOUNT;

        $donationExpiry = null;
        if (!$isLifetime) {
            $numberOfMonthsOfPerks = floor($amountInDollars / Donation::ONE_MONTH_REQUIRED_AMOUNT);
            $donationExpiry = now()->addMonths($numberOfMonthsOfPerks);
        }

        $donation = null;
        DB::beginTransaction();
        try {
            $donation = Donation::create([
                'account_id' => $accountId,
                'amount' => $amountInDollars,
                'perks_end_at' => $donationExpiry,
                'is_lifetime_perks' => $isLifetime,
                'is_active' => true,
            ]);

            AccountPayment::create([
                'payment_type' => AccountPaymentType::Donation,
                'payment_id' => $donation->getKey(),
                'payment_amount' => $amountInCents,
                'payment_source' => $transactionId,
                'account_id' => $accountId,
                'is_processed' => true,
                'is_refunded' => false,
                'is_subscription_payment' => false,
            ]);

            $session->is_processed = true;
            $session->save();

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        // Add user to Donator group if they're logged in
        if ($session->account !== null) {
            $donatorGroup = Group::where('name', 'donator')->first();
            $donatorGroupId = $donatorGroup->getKey();

            if (!$session->account->groups->contains($donatorGroupId)) {
                $session->account->groups()->attach($donatorGroupId);
            }
        }
    }
}
