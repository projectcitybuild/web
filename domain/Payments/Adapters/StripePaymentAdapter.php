<?php

namespace Domain\Payments\Adapters;

use Domain\Payments\PaymentAdapter;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

final class StripePaymentAdapter implements PaymentAdapter
{
    private string $currency;

    public function __construct(string $stripeSecret, string $currency = 'usd')
    {
        $this->currency = $currency;

        if (empty($stripeSecret)) {
            throw new \Exception('No Stripe API secret set');
        }
        Stripe::setApiKey($stripeSecret);
    }

    public function createCheckoutSession(string $uniqueSessionId, string $productId, int $quantity = 1): string
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'client_reference_id' => $uniqueSessionId,
            'line_items' => [
                [
                    'price' => $productId, // Stripe's "price id" for a product
                    'quantity' => $quantity,
                ],
            ],
            // TODO: replace with Enum
            'mode' => 'payment', // 'payment' or 'subscription'
            'success_url' => route('front.donate.success'),
            'cancel_url' => route('front.donate'),
        ]);
    }
}
