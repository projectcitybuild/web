<?php

namespace App\Domains\Donations\Data\Payloads;

/**
 * Represents the payload for a Stripe Checkout event
 *
 * @see https://docs.stripe.com/api/checkout/sessions/retrieve
 */
final class StripeCheckoutSession
{
    public function __construct(
        public string $customerId,
        public string $sessionId,
    ) {}

    public static function fromJson(array $payload): StripeCheckoutSession
    {
        $object = $payload['data']['object'];
        $sessionId = $object['id'];

        return new StripeCheckoutSession(
            customerId: $object['customer'],
            sessionId: $sessionId,
        );
    }
}
