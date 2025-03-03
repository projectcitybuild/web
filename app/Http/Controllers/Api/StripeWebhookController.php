<?php

namespace App\Http\Controllers\Api;

use App\Core\Data\Exceptions\BadRequestException;
use App\Core\Domains\Auditing\Causers\SystemCauser;
use App\Core\Domains\Auditing\Causers\SystemCauseResolver;
use App\Domains\Donations\Data\Payloads\StripeCheckoutLineItem;
use App\Domains\Donations\Data\Payloads\StripeCheckoutSession;
use App\Domains\Donations\Data\Payloads\StripeInvoicePaid;
use App\Domains\Donations\Data\Payloads\StripePaginatedResponse;
use App\Domains\Donations\Data\PaymentType;
use App\Domains\Donations\Exceptions\StripeProductNotFoundException;
use App\Domains\Donations\UseCases\ProcessPayment;
use App\Models\Account;
use Exception;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;

final class StripeWebhookController extends CashierController
{
    public function __construct(
        private readonly ProcessPayment $processPaymentUseCase,
        private readonly StripeClient $stripeClient,
    ) {
        SystemCauseResolver::setCauser(SystemCauser::STRIPE_WEBHOOK);
        parent::__construct();
    }

    /**
     * Handles one-time payments, or the first payment of a subscription.
     *
     * @throws StripeProductNotFoundException if productId does not exist in the StripeProducts table
     * @throws Exception if user cannot be found
     */
    public function handleCheckoutSessionCompleted(array $payload): Response
    {
        Log::info('[webhook] checkout.session_completed', ['payload' => $payload]);

        $session = StripeCheckoutSession::fromJson(
            StripePaginatedResponse::fromJson($payload)->data,
        );
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

        /** @var Account $account */
        $account = $this->getUserByStripeId($session->customerId)
            ?? throw new Exception('Could not find user matching customer id: '.$session->customerId);

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

        return $this->successMethod();
    }

    /**
     * Handle subsequent subscription payments (after the first).
     *
     * @throws StripeProductNotFoundException if productId does not exist in the StripeProducts table
     * @throws BadRequestException if amount or paidQuantity is invalid
     * @throws Exception if user cannot be found
     */
    public function handleInvoicePaid(array $payload): Response
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

        return $this->successMethod();
    }
}
