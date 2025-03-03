<?php

namespace App\Domains\Donations\Listeners;

use App\Core\Data\Exceptions\BadRequestException;
use App\Domains\Donations\Data\Payloads\StripeCheckoutLineItem;
use App\Domains\Donations\Data\Payloads\StripeCheckoutSession;
use App\Domains\Donations\Data\Payloads\StripeInvoicePaid;
use App\Domains\Donations\Data\Payloads\StripePaginatedResponse;
use App\Domains\Donations\Data\PaymentType;
use App\Domains\Donations\Exceptions\StripeProductNotFoundException;
use App\Models\Account;
use Exception;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\StripeClient;


class StripeEventListener
{
    public function __construct(
        private readonly StripeClient $stripeClient,
    ) {}

    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'checkout.session.completed') {
            $this->handleCheckoutSessionCompleted($event->payload);
        }
        if ($event->payload['type'] === 'invoice.paid') {
            $this->handleInvoicePaid($event->payload);
        }
    }

    /**
     * Handles one-time payments, or the first payment of a subscription.
     *
     * @throws StripeProductNotFoundException if productId does not exist in the StripeProducts table
     * @throws Exception if user cannot be found
     */
    private function handleCheckoutSessionCompleted(array $payload)
    {
        Log::info('[webhook] checkout.session.completed', ['payload' => $payload]);

        $session = StripeCheckoutSession::fromJson($payload);

        $lineItems = StripeCheckoutLineItem::fromJson(
            StripePaginatedResponse::fromJson(
                $this->stripeClient->checkout->sessions->allLineItems(
                    id: $session->sessionId,
                    params: ['limit' => 1],
                )->toArray(),
            )->data,
        );

        Log::debug('Parsed Stripe responses', [
            'session' => $session,
            'line_items' => $lineItems,
        ]);

//        /** @var Account $account */
        $account = Cashier::findBillable($session->customerId);
        Log::info($account);

        // Subscription payments are handled by `handleInvoicePaid` (`invoice.paid` event)
        // which is also sent at the same time
//        if ($event->paymentType->isSubscription()) {
//            return $this->successMethod();  // Must return success or Stripe will think we failed to receive the payload
//        }
//
//        $this->processPaymentUseCase->execute(
//            account: $account,
//            productId: $event->productId,
//            priceId: $event->priceId,
//            paidAmount: $event->paidAmount,
//            quantity: $event->quantity,
//            donationType: PaymentType::ONE_TIME,
//        );
    }

    /**
     * Handle subsequent subscription payments (after the first).
     *
     * @throws StripeProductNotFoundException if productId does not exist in the StripeProducts table
     * @throws BadRequestException if amount or paidQuantity is invalid
     * @throws Exception if user cannot be found
     */
    private function handleInvoicePaid(array $payload)
    {
        Log::info('[webhook] invoice.paid', ['payload' => $payload]);

        $event = StripeInvoicePaid::fromPayload($payload);

        /** @var Account $account */
        $account = $this->getUserByStripeId($event->customerId)
            ?? throw new Exception('Could not find user matching customer id: '.$event->customerId);

        $this->processPaymentUseCase->execute(
            account: $account,
            productId: $event->productId,
            priceId: $event->priceId,
            paidAmount: $event->paidAmount,
            quantity: $event->quantity,
            donationType: PaymentType::SUBSCRIPTION,
        );
    }
}
