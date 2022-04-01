<?php

namespace App\Http\Controllers\Api\v1;

use Domain\Donations\Entities\Denomination;
use Domain\Donations\Entities\DonationType;
use Domain\Donations\Entities\Payloads\StripeCheckoutSessionCompleted;
use Domain\Donations\Entities\Payloads\StripeInvoicePaid;
use Domain\Donations\Entities\PaidAmount;
use Domain\Donations\UseCases\ProcessPaymentUseCase;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;

final class StripeWebhookController extends CashierController
{
    public function __construct(
        private ProcessPaymentUseCase $processPaymentUseCase,
        private StripeClient $stripeClient
    ) {}

    /**
     * Handle Checkout complete events and fulfil one-time payments
     * or the first payment of a subscription.
     */
    public function handleCheckoutSessionCompleted(array $payload): Response
    {
        Log::info('[webhook] checkout.session_completed', ['payload' => $payload]);

        $event = StripeCheckoutSessionCompleted::fromPayload($payload, stripeClient: $this->stripeClient);

        $account = $this->getUserByStripeId($event->customerId)
            ?? throw new \Exception('Could not find user matching customer id: '.$event->customerId);

        // Subscription payments are handled by handleInvoicePaid (`invoice.paid` event)
        if ($event->paymentType->isSubscription()) {
            // Must return success or Stripe will think we failed to receive the payload
            return $this->successMethod();
        }

        // Sanity checks
        if ($event->donationTierId == null) {
            throw new \Exception('No `donation_tier_id` defined in Stripe metadata for this Price model');
        }
        if ($event->paidAmount->toCents() <= 0) {
            throw new \Exception('Amount paid was zero');
        }
        if ($event->quantity <= 0) {
            throw new \Exception('Quantity purchased was zero');
        }

        $this->processPaymentUseCase->execute(
            account: $account,
            productId: $event->productId,
            priceId: $event->priceId,
            donationTierId: $event->donationTierId,
            paidAmount: $event->paidAmount,
            quantity: $event->quantity,
            donationType: DonationType::ONE_OFF,
        );

        return $this->successMethod();
    }

    /**
     * Handle subsequent subscription payments (after the first).
     */
    public function handleInvoicePaid(array $payload): Response
    {
        Log::info('[webhook] invoice.paid', ['payload' => $payload]);

        $event = StripeInvoicePaid::fromPayload($payload);

        $account = $this->getUserByStripeId($event->customerId)
            ?? throw new \Exception('Could not find user matching customer id: '.$event->customerId);

        // Sanity checks
        if ($event->donationTierId == null) {
            throw new \Exception('No donation_tier_id defined in Stripe metadata for this Price');
        }
        if ($event->paidAmount->toCents() <= 0) {
            throw new \Exception('Amount paid was zero');
        }
        if ($event->quantity <= 0) {
            throw new \Exception('Quantity purchased was zero');
        }

        $this->processPaymentUseCase->execute(
            account: $account,
            productId: $event->productId,
            priceId: $event->priceId,
            donationTierId: $event->donationTierId,
            paidAmount: $event->paidAmount,
            quantity: $event->quantity,
            donationType: DonationType::SUBSCRIPTION,
        );

        return $this->successMethod();
    }
}
