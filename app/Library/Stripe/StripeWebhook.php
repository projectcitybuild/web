<?php

namespace App\Library\Stripe;

final class StripeWebhook
{
    private $event;
    private $sessionId;
    private $transactionId;
    private $amountInCents;

    public function __construct(StripeWebhookEvent $event, string $transactionId)
    {
        $this->event = $event;
        $this->transactionId = $transactionId;
    }

    public static function fromJSON($json): ?StripeWebhook
    {
        // Only parse Webhook events we care about
        $webhookEvent = null;
        try {
            $webhookEvent = StripeWebhookEvent::fromRawValue($json->type);
        } catch (\Exception $e) {
            return null;
        }

        $webhook = new StripeWebhook($webhookEvent, $json->data->object->id);

        $items = $json->data->object->display_items;
        if ($items !== null && count($items) > 0) {
            $webhook->setAmountInCents($json->data->object->display_items[0]->amount);
        }

        $sessionId = $json->data->object->client_reference_id;
        if ($sessionId !== null) {
            $webhook->setSessionId($sessionId);
        }

        return $webhook;
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

    public function setAmountInCents(int $amountInCents)
    {
        $this->amountInCents = $amountInCents;
    }

    public function setSessionId(string $sessionId)
    {
        $this->sessionId = $sessionId;
    }
}
