<?php

namespace App\Http\Controllers\Api\v1;

use Domain\Donations\DonationService;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

final class StripeWebhookController extends CashierController
{
    private DonationService $donationService;
    private StripeClient $stripeClient;

    public function __construct(DonationService $donationService, StripeClient $stripeClient)
    {
        $this->donationService = $donationService;
        $this->stripeClient = $stripeClient;
    }

    /**
     * Handle Checkout complete events and fulfil one-time payments
     * or the first payment of a subscription
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleCheckoutSessionCompleted(array $payload)
    {
        Log::info('[webhook] checkout.session_completed', ['payload' => $payload]);

        $customerId = $payload['data']['object']['customer'];
        $account = $this->getUserByStripeId($customerId);

        if ($account === null) {
            Log::warning('Could not find user matching customer id: '.$customerId);

            return $this->successMethod();
        }

        // `checkout_complete` events don't give us Line item data by default, so we need
        // to unfortunately fetch this ourselves
        $sessionId = $payload['data']['object']['id'];
        $lineItems = $this->stripeClient->checkout->sessions
            ->allLineItems($sessionId, ['limit' => 1]);

        Log::info('Retrieved line items', ['line_items' => $lineItems]);

        $firstLine = $lineItems['data'][0];
        $amountPaidInCents = $firstLine['amount_total'];
        $quantity = $firstLine['quantity'];
        $priceData = $firstLine['price'];
        $productId = $priceData['product'];
        $priceId = $priceData['id'];
        $isSubscriptionPayment = $priceData['type'] === 'recurring';

        // Sanity checks
        if ($amountPaidInCents <= 0) {
            throw new \Exception('Amount paid was zero');
        }
        if ($quantity <= 0) {
            throw new \Exception('Quantity purchased was zero');
        }

        $this->donationService->processPayment(
            $account,
            $productId,
            $priceId,
            $amountPaidInCents,
            $quantity,
            $isSubscriptionPayment
        );

        return $this->successMethod();
    }

    /**
     * Handle subsequent subscription payments (after the first)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleInvoicePaid(array $payload)
    {
        Log::info('[webhook] invoice.paid', ['payload' => $payload]);

        $customerId = $payload['data']['object']['customer'];
        $account = $this->getUserByStripeId($customerId);

        if ($account === null) {
            Log::warning('Could not find user matching customer id: '.$customerId);

            return $this->successMethod();
        }

        $object = $payload['data']['object'];
        $amountPaidInCents = $object['total'];

        $firstLine = $object['lines']['data'][0];
        $quantity = $firstLine['quantity'];
        $priceData = $firstLine['price'];
        $productId = $priceData['product'];
        $priceId = $priceData['id'];
        $isSubscriptionPayment = $priceData['type'] === 'recurring';

        // Sanity checks
        if ($amountPaidInCents <= 0) {
            throw new \Exception('Amount paid was zero');
        }
        if ($quantity <= 0) {
            throw new \Exception('Quantity purchased was zero');
        }

        $this->donationService->processPayment(
            $account,
            $productId,
            $priceId,
            $amountPaidInCents,
            $quantity,
            $isSubscriptionPayment
        );

        return $this->successMethod();
    }
}
