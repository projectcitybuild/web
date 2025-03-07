<?php

namespace App\Core\Domains\Payment\Listeners;

use App\Core\Domains\Payment\Data\Stripe\StripeCheckoutLineItem;
use App\Core\Domains\Payment\Data\Stripe\StripeCheckoutSession;
use App\Core\Domains\Payment\Data\Stripe\StripeInvoicePaid;
use App\Core\Domains\Payment\Events\PaymentCreated;
use App\Domains\Donations\UseCases\RecordPayment;
use App\Models\Account;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\StripeClient;

class StripeEventListener
{
    public function __construct(
        private readonly StripeClient $stripeClient,
        private readonly RecordPayment $recordPayment,
    ) {}

    public function handle(WebhookReceived $event): void
    {
        $type = $event->payload['type'];

        if ($type === 'checkout.session.completed') {
            $this->handleCheckoutSessionCompleted($event->payload);
        }
        if ($type === 'invoice.paid') {
            $this->handleInvoicePaid($event->payload);
        }
    }

    /**
     * Handles one-time payments.
     *
     * Technically this handles all payment types, as we funnel everything
     * through Checkout. However, we abort early for subscriptions as an
     * `invoice.paid` event is sent along with it.
     */
    private function handleCheckoutSessionCompleted(array $payload): void
    {
        Log::info('[webhook] checkout.session.completed', ['payload' => $payload]);

        $session = StripeCheckoutSession::fromJson($payload);

        $lineItems = $this->stripeClient->checkout->sessions->allLineItems(
            id: $session->sessionId,
            params: ['limit' => 1],
        );
        $lineItem = StripeCheckoutLineItem::fromJson(
            $lineItems->toArray()['data'][0], // Only 1 line item for a donation
        );

        Log::debug(
            'Parsed Stripe responses',
            compact('session', 'lineItem'),
        );

        /** @var ?Account $account */
        $account = Cashier::findBillable($session->customerId);

        // Subscription payments are handled by `handleInvoicePaid` (`invoice.paid` event)
        // which is also sent at the same time
        if ($lineItem->paymentType->isSubscription()) {
            return;
        }

        $payment = $this->recordPayment->saveLineItem($lineItem, account: $account);

        PaymentCreated::dispatch($payment);
    }

    /**
     * Handles subscription payments
     */
    private function handleInvoicePaid(array $payload): void
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
