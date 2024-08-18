<?php

namespace App\Domains\Donations\Entities\Payloads;

use App\Domains\Donations\Entities\PaidAmount;
use App\Domains\Donations\Entities\PaymentType;
use Stripe\StripeClient;

final class StripeCheckoutSessionCompleted
{
    public function __construct(
        public string $customerId,
        public string $sessionId,
        public PaidAmount $paidAmount,
        public int $quantity,
        public string $productId,
        public string $priceId,
        public PaymentType $paymentType,
    ) {
    }

    public static function fromPayload(array $payload, StripeClient $stripeClient): StripeCheckoutSessionCompleted
    {
        $object = $payload['data']['object'];
        $sessionId = $object['id'];

        // `checkout_complete` events don't give us line_items, so we need to fetch this ourselves
        $lineItems = $stripeClient->checkout->sessions->allLineItems(
            id: $sessionId,
            params: ['limit' => 1],
        );

        $firstLine = $lineItems['data'][0];
        $price = $firstLine['price'];

        return new StripeCheckoutSessionCompleted(
            customerId: $object['customer'],
            sessionId: $sessionId,
            paidAmount: PaidAmount::fromCents($firstLine['amount_total']),
            quantity: $firstLine['quantity'],
            productId: $price['product'],
            priceId: $price['id'],
            paymentType: $price['type'] == 'recurring'
                ? PaymentType::SUBSCRIPTION
                : PaymentType::ONE_OFF,
        );
    }
}
