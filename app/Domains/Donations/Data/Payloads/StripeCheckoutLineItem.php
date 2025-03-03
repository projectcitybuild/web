<?php

namespace App\Domains\Donations\Data\Payloads;

use App\Domains\Donations\Data\Amount;
use App\Domains\Donations\Data\PaymentType;

/**
 * Represents the first handful of items for a [StripeCheckoutSession]
 *
 * @see https://docs.stripe.com/api/checkout/sessions/line_items
 * @see https://docs.stripe.com/api/invoice-line-item/object
 */
final class StripeCheckoutLineItem
{
    public function __construct(
        public int $quantity,
        public Amount $paidAmount,          // Amount the user actually paid
        public Amount $originalAmount,      // Amount the price was set in and expect to receive
        public string $productId,           // Stripe product id
        public string $priceId,             // Stripe price id
        public PaymentType $paymentType,
    ) {}

    public static function fromJson(array $json): StripeCheckoutLineItem
    {
        $price = $json['price'];

        return new StripeCheckoutLineItem(
            quantity: $json['quantity'],
            paidAmount: new Amount(
                currency: $json['currency'],
                value: $json['amount_total'],
            ),
            originalAmount: new Amount(
                currency: $price['currency'],
                value: $price['unit_amount'],
            ),
            productId: $price['product'],
            priceId: $price['id'],
            paymentType: PaymentType::fromString($price['type']),
        );
    }
}
