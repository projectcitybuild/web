<?php

namespace App\Core\Domains\Payment\Data\Stripe;

use App\Core\Domains\Payment\Data\PaymentType;
use Money\Currency;
use Money\Money;
use Stripe\LineItem;

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
        public Money $unitAmount,           // Amount the user paid (in their currency)
        public string $productId,           // Stripe product id
        public string $priceId,             // Stripe price id
        public PaymentType $paymentType,
    ) {}

    public static function fromLineItem(LineItem $lineItem): StripeCheckoutLineItem
    {
        $price = $lineItem->price;

        return new StripeCheckoutLineItem(
            quantity: $lineItem->quantity,
            unitAmount: new Money($price->unit_amount, new Currency($price->currency)),
            productId: $price->product,
            priceId: $price->id,
            paymentType: PaymentType::fromString($price->type),
        );
    }
}
