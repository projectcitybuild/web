<?php

namespace App\Core\Domains\Payment\Data\Stripe;

use App\Core\Domains\Payment\Data\PaymentType;
use Money\Currency;
use Money\Money;
use Stripe\Price;

class StripePrice
{
    public function __construct(
        public string $id,
        public string $productId,
        public Money $unitAmount,
        public PaymentType $paymentType,
    ) {}

    public static function fromPrice(Price $price): StripePrice
    {
        return new StripePrice(
            id: $price->id,
            productId: $price->product,
            unitAmount: new Money($price->unit_amount, new Currency($price->currency)),
            paymentType: PaymentType::fromString($price->type),
        );
    }
}
