<?php

namespace App\Library\Stripe;

final class StripeWebhookPayload
{
    public StripeWebhookEvent $event;
    public string $sessionId;
    public string $transactionId;
    public int $amountPaidInCents;

    public function __construct(
        StripeWebhookEvent $event,
        string $sessionId,
        string $transactionId,
        int $amountPaidInCents
    ) {
        $this->event = $event;
        $this->sessionId = $sessionId;
        $this->transactionId = $transactionId;
        $this->amountPaidInCents = $amountPaidInCents;
    }
}
