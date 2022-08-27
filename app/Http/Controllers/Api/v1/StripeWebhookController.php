<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\Http\BadRequestException;
use Domain\Donations\Entities\Payloads\StripeCheckoutSessionCompleted;
use Domain\Donations\Entities\Payloads\StripeInvoicePaid;
use Domain\Donations\Entities\PaymentType;
use Domain\Donations\Exceptions\StripeProductNotFoundException;
use Domain\Donations\UseCases\ProcessPaymentUseCase;
use Entities\Models\Eloquent\Account;
use Exception;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Library\Auditing\Causers\SystemCauser;
use Library\Auditing\Causers\SystemCauseResolver;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;

final class StripeWebhookController extends CashierController
{
    public function __construct(
        private ProcessPaymentUseCase $processPaymentUseCase,
        private StripeClient $stripeClient,
    ) {
        SystemCauseResolver::setCauser(SystemCauser::STRIPE_WEBHOOK);
        parent::__construct();
    }

    /**
     * Handle Checkout complete events and fulfil one-time payments
     * or the first payment of a subscription.
     *
     * @throws StripeProductNotFoundException if productId does not exist in the StripeProducts table
     * @throws BadRequestException if amount or paidQuantity is invalid
     * @throws Exception if user cannot be found
     */
    public function handleCheckoutSessionCompleted(array $payload): Response
    {
        Log::info('[webhook] checkout.session_completed', ['payload' => $payload]);

        $event = StripeCheckoutSessionCompleted::fromPayload($payload, stripeClient: $this->stripeClient);

        /** @var Account $account */
        $account = $this->getUserByStripeId($event->customerId)
            ?? throw new Exception('Could not find user matching customer id: '.$event->customerId);

        // Subscription payments are handled by `handleInvoicePaid` (`invoice.paid` event)
        // which is also sent at the same time
        if ($event->paymentType->isSubscription()) {
            return $this->successMethod();  // Must return success or Stripe will think we failed to receive the payload
        }

        $this->processPaymentUseCase->execute(
            account: $account,
            productId: $event->productId,
            priceId: $event->priceId,
            paidAmount: $event->paidAmount,
            quantity: $event->quantity,
            donationType: PaymentType::ONE_OFF,
        );

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
