<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Payments\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Stripe\StripeClient;

final class StripeWebhookController extends CashierController
{
    /**
     * Handle Checkout complete events and fulfil payments
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleCheckoutSessionCompleted(array $payload)
    {
        Log::info('[webhook] checkout.session_completed', ['payload' => $payload]);

        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        $sessionId = $payload['data']['object']['id'];

        $stripe = new StripeClient(config('services.stripe.secret'));
        $lineItems = $stripe->checkout->sessions->allLineItems($sessionId, ['limit' => 1]);

        Log::info('Retrieved line items', ['line_items' => $lineItems]);

        $firstLine = $lineItems['data'][0];

        Payment::create([
            'account_id' => $user !== null ? $user->getKey() : null,
            'stripe_price' => $firstLine['price']['id'],
            'stripe_product' => $firstLine['price']['product'],
            'amount_paid_in_cents' => $firstLine['amount_total'],
            'quantity' => $firstLine['quantity'],
            'is_subscription_payment' => $firstLine['price']['type'] === 'recurring',
        ]);

        return $this->successMethod();
    }
}
