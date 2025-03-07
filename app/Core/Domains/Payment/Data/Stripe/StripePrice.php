<?php

namespace App\Core\Domains\Payment\Data\Stripe;

use App\Core\Domains\Payment\Data\PaymentType;

class StripePrice
{
    public function __construct(
        public string $id,
        public string $productId,
        public PaymentType $paymentType,
    ) {}

    public static function fromPayload(array $payload): StripePrice
    {
        return new StripePrice(
          id: $payload['id'],
          productId: $payload['product'],
          paymentType: $payload['recurring'] !== null
              ? PaymentType::SUBSCRIPTION
              : PaymentType::ONE_TIME,
        );
    }
}
