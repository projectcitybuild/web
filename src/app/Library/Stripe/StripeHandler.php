<?php

namespace App\Library\Stripe;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Webhook;

class StripeHandler
{
    private $currency = 'aud';

    public function __construct()
    {
        $this->setApiKey();
        $this->setCurrency($this->currency);
    }

    private function setApiKey()
    {
        $key = config('services.stripe.secret');
        if (empty($key)) {
            throw new \Exception('No Stripe API secret set');
        }
        Stripe::setApiKey($key);
    }

    public function setCurrency(string $currency) : StripeHandler
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Creates a new session for using Stripe's Checkout flow
     *
     * @param string $uniqueSessionId   A unique UUID that will be sent to Stripe to be stored alongside the
     *                                  session. When Stripe notifies us via WebHook that the payment is processed,
     *                                  they will pass us back the UUID so we can fulfill the purchase.
     * @return string Stripe session ID
     */
    public function createCheckoutSession(string $uniqueSessionId, int $amountInCents): string
    {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'client_reference_id' => $uniqueSessionId,
            'line_items' => [
                [
                    'name' => 'PCB Contribution',
                    'description' => 'One time payment',
                    'images' => [],
                    'amount' => $amountInCents,
                    'currency' => $this->currency,
                    'quantity' => 1,
                ],
            ],
            'success_url' => route('front.donate.success'),
            'cancel_url' => route('front.donate'),
        ]);

        return $session->id;
    }

    /**
     * Receives a Webhook from Stripe and parses it into a generalized model
     *
     * @param string $payload
     * @param string $signature
     * @param string $secret
     * @return StripeWebhook
     * @throws \Stripe\Error\SignatureVerification
     *
     * Example Webhook Payload:
     * {
     *    "created": 1326853478,
     *    "livemode": false,
     *    "id": "evt_00000000000000",
     *    "type": "checkout.session.completed",
     *    "object": "event",
     *    "request": null,
     *    "pending_webhooks": 1,
     *    "api_version": "2018-07-27",
     *    "data": {
     *       "object": {
     *           "id": "cs_00000000000000",
     *           "object": "checkout.session",
     *           "billing_address_collection": null,
     *           "cancel_url": "https://example.com/cancel",
     *           "client_reference_id": null,
     *           "customer": null,
     *           "customer_email": null,
     *           "display_items": [
     *               {
     *                   "amount": 1500,
     *                   "currency": "usd",
     *                   "custom": {
     *                       "description": "Comfortable cotton t-shirt",
     *                       "images": null,
     *                       "name": "T-shirt"
     *                   },
     *                   "quantity": 2,
     *                   "type": "custom"
     *               }
     *           ],
     *           "livemode": false,
     *           "locale": null,
     *           "mode": null,
     *           "payment_intent": "pi_00000000000000",
     *           "payment_method_types": [
     *               "card"
     *           ],
     *           "setup_intent": null,
     *           "submit_type": null,
     *           "subscription": null,
     *           "success_url": "https://example.com/success"
     *       }
     *    }
     * }
     */
    public function getWebhookEvent(string $payload, string $signature, string $secret): ?StripeWebhook
    {
        $event = Webhook::constructEvent($payload, $signature, $secret);

        // Only parse Webhook events we care about
        $webhookEvent = null;
        try {
            $webhookEvent = StripeWebhookEvent::fromRawValue($event->type);
        } catch(\Exception $e) {
            return null;
        }

        $webhook = new StripeWebhook(
            $webhookEvent,
            $event->data->object->id,
            $event->data->object->client_reference_id,
            $event->data->object->display_items[0]->amount
        );
        return $webhook;
    }
}