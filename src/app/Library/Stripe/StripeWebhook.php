<?php

namespace App\Library\Stripe;

final class StripeWebhook
{
    private $event;
    private $transactionId;
    private $amountInCents;

    public function __construct(StripeWebhookEvent $event, string $transactionId, int $amountInCents)
    {
        $this->event = $event;
        $this->transactionId = $transactionId;
        $this->amountInCents = $amountInCents;
    }

    public function getEvent(): StripeWebhookEvent
    {
        return $this->event;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }
}