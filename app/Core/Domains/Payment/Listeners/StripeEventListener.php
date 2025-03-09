<?php

namespace App\Core\Domains\Payment\Listeners;

use App\Core\Domains\Payment\Data\PaymentType;
use App\Core\Domains\Payment\Events\PaymentCreated;
use App\Core\Domains\Payment\UseCases\RecordPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;
use Money\Currency;
use Money\Money;
use Stripe\Checkout\Session;
use Stripe\Invoice;
use Stripe\StripeClient;

/**
 * Listens to Stripe webhook events and records any payments.
 *
 * It then dispatches a PaymentCreated event so that specific
 * domains can actually fulfill the payments downstream.
 */
class StripeEventListener implements ShouldQueue
{
    public function __construct(
        private readonly StripeClient $stripeClient,
        private readonly RecordPayment $recordPayment,
    ) {}

    public function handle(WebhookReceived $event): void
    {
        $type = $event->payload['type'];

        if ($type === 'checkout.session.completed') {
            $this->checkoutSessionCompleted($event->payload);
        }
        if ($type === 'invoice.paid') {
            $this->invoicePaid($event->payload);
        }
    }

    /**
     * Handles one-time payments.
     *
     * Technically this handles subscriptions as well because we funnel everything
     * through Checkout initially. However, the `invoice.paid` event is sent for any
     * subscription payment (including the first), so there's no point handling
     * subscription logic here.
     */
    private function checkoutSessionCompleted(array $payload): void
    {
        Log::info('[webhook] checkout.session.completed', compact('payload'));

        $session = Session::constructFrom($payload['data']['object']);

        $lineItems = $this->stripeClient->checkout->sessions->allLineItems(
            id: $session->id,
            // Only 1 line item for a donation
            params: ['limit' => 1],
        );

        // Get the purchased items for the Checkout session.
        $lineItem = $lineItems->first();

        // Subscription payments will always have an `invoice.paid` event,
        // so handling it here is redundant
        $paymentType = PaymentType::fromString($lineItem->price->type);
        if ($paymentType->isSubscription()) {
            return;
        }

        // The price plan we created in Stripe for this purchased item
        $originalPrice = $this->stripeClient->prices->retrieve($lineItem->price->id);

        $payment = $this->recordPayment->save(
            customerId: $session->customer,
            productId: $lineItem->price->product,
            priceId: $lineItem->price->id,
            paidUnitAmount: new Money($lineItem->price->unit_amount, new Currency($lineItem->price->currency)),
            originalUnitAmount: new Money($originalPrice->unit_amount, new Currency($originalPrice->currency)),
            unitQuantity: $lineItem->quantity,
        );

        PaymentCreated::dispatch($payment);
    }

    /**
     * Handles subscription payments
     */
    private function invoicePaid(array $payload): void
    {
        Log::info('[webhook] invoice.paid', compact('payload'));

        $invoice = Invoice::constructFrom($payload['data']['object']);

        // Only 1 line item for a donation
        $lineItem = $invoice->lines->first();

        // The price plan we created in Stripe for this purchased item
        $originalPrice = $this->stripeClient->prices->retrieve($lineItem->price->id);

        $payment = $this->recordPayment->save(
            customerId: $invoice->customer,
            productId: $lineItem->price->product,
            priceId: $lineItem->price->id,
            paidUnitAmount: new Money($lineItem->price->unit_amount, new Currency($lineItem->price->currency)),
            originalUnitAmount: new Money($originalPrice->unit_amount, new Currency($originalPrice->currency)),
            unitQuantity: $lineItem->quantity,
        );

        PaymentCreated::dispatch($payment);
    }
}
