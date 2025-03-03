<?php

namespace App\Domains\Donations\Data\Payloads;

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
        public string $paidCurrency,        // Currency the user actually paid with
        public int $paidAmount,             // Amount the user actually paid
        public string $originalCurrency,    // Currency the price was set in and expect to receive
        public int $originalAmount,         // Amount the price was set in and expect to receive
        public string $productId,           // Stripe product id
        public string $priceId,             // Stripe price id
        public PaymentType $paymentType,
    ) {}

    public static function fromJson(array $payload): StripeCheckoutLineItem
    {
        $price = $payload['price'];

        return new StripeCheckoutLineItem(
            quantity: $payload['quantity'],
            paidCurrency: $payload['currency'],
            paidAmount: $payload['amount_total'],
            originalCurrency: $price['currency'],
            originalAmount: $price['unit_amount'],
            productId: $price['product'],
            priceId: $price['id'],
            paymentType: PaymentType::fromString($price['type']),
        );
    }
}
