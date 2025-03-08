<?php

namespace App\Core\Domains\Payment\Listeners;

use App\Core\Domains\Payment\Data\Stripe\StripeCheckoutLineItem;
use App\Core\Domains\Payment\Data\Stripe\StripeCheckoutSession;
use App\Core\Domains\Payment\Data\Stripe\StripeInvoicePaid;
use App\Core\Domains\Payment\Data\Stripe\StripePrice;
use App\Core\Domains\Payment\Events\PaymentCreated;
use App\Core\Domains\Payment\UseCases\RecordPayment;
use App\Models\Account;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;
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
        Log::info('[webhook] checkout.session.completed', ['payload' => $payload]);

        $session = StripeCheckoutSession::fromJson($payload);

        // Get the purchased items for the Checkout session
        $lineItems = $this->stripeClient->checkout->sessions->allLineItems(
            id: $session->sessionId,
            params: ['limit' => 1],
        );
        $lineItem = StripeCheckoutLineItem::fromLineItem(
            $lineItems->data[0], // Only 1 line item for a donation
        );

        // Subscription payments will always have an `invoice.paid` event,
        // so handling it here is redundant
        if ($lineItem->paymentType->isSubscription()) {
            return;
        }

        // Get the price plan we set on Stripe
        $price = StripePrice::fromPrice(
            $this->stripeClient->prices->retrieve($lineItem->priceId),
        );

        Log::debug(
            'Parsed Stripe responses',
            compact('session', 'lineItem', 'price'),
        );

        $payment = $this->recordPayment->save(
            customerId: $session->customerId,
            productId: $lineItem->productId,
            priceId: $lineItem->priceId,
            paidUnitAmount: $lineItem->unitAmount,
            originalUnitAmount: $price->unitAmount,
            unitQuantity: $lineItem->quantity,
        );

        PaymentCreated::dispatch($payment);
    }

    /**
     * Handles subscription payments
     */
    private function invoicePaid(array $payload): void
    {
        Log::info('[webhook] invoice.paid', ['payload' => $payload]);

        $invoicePaid = StripeInvoicePaid::fromPayload($payload);

        /** @var ?Account $account */
        $account = Cashier::findBillable($invoicePaid->customerId);

        $this->recordPayment->saveLineItem($lineItem, account: $account);

//        $this->processPaymentUseCase->execute(
//            account: $account,
//            productId: $event->productId,
//            priceId: $event->priceId,
//            paidAmount: $event->paidAmount,
//            quantity: $event->quantity,
//            donationType: PaymentType::SUBSCRIPTION,
//        );
    }
}
