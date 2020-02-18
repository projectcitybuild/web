<?php

namespace App\Library\Stripe;

final class StripeWebhook
{
    private $event;
    private $sessionId;
    private $transactionId;
    private $amountInCents;

    public function __construct(StripeWebhookEvent $event, string $transactionId, string $sessionId, int $amountInCents)
    {
        $this->event = $event;
        $this->transactionId = $transactionId;
        $this->sessionId = $sessionId;
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

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }
}