<?php

namespace Domain\Donations\Entities\Payloads;

use Domain\Donations\Entities\PaymentType;
use Domain\Donations\Entities\PaidAmount;

final class StripeInvoicePaid
{
    public function __construct(
        public string $customerId,
        public string $sessionId,
        public PaidAmount $paidAmount,
        public int $quantity,
        public string $productId,
        public string $priceId,
        public PaymentType $paymentType,
    ) {}

    public static function fromPayload(array $payload): StripeInvoicePaid
    {
        $object = $payload['data']['object'];
        $sessionId = $object['id'];
        $lineItems = $object['lines'];
        $firstLine = $lineItems['data'][0];
        $price = $firstLine['price'];

        return new StripeInvoicePaid(
            customerId: $object['customer'],
            sessionId: $sessionId,
            paidAmount: PaidAmount::fromCents($object['total']),
            quantity: $firstLine['quantity'],
            productId: $price['product'],
            priceId: $price['id'],
            paymentType: $price['type'] == 'recurring'
                ? PaymentType::SUBSCRIPTION
                : PaymentType::ONE_OFF,
        );
    }
}
