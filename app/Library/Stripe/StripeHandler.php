<?php

namespace App\Library\Stripe;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeHandler
{
    private $currency = 'usd';

    public function __construct()
    {
        $this->setApiKey();
        $this->setCurrency($this->currency);
    }

    public function setCurrency(string $currency): StripeHandler
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Creates a new session for using Stripe's Checkout flow.
     *
     * @param string $uniqueSessionId A unique UUID that will be sent to Stripe to be stored alongside the
     *                                  session. When Stripe notifies us via WebHook that the payment is processed,
     *                                  they will pass us back the UUID so we can fulfill the purchase.
     *
     * @return Session Stripe session
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCheckoutSession(string $uniqueSessionId, string $stripePriceId, int $quantity = 1): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'client_reference_id' => $uniqueSessionId,
            'line_items' => [
                [
                    'price' => $stripePriceId,
                    'quantity' => $quantity,
                ],
            ],
            // TODO: replace with Enum
            'mode' => 'payment', // 'payment' or 'subscription'
            'success_url' => route('front.donate.success'),
            'cancel_url' => route('front.donate'),
        ]);
    }

    /**
     * Receives a Webhook from Stripe and parses it into a generalized model.
     *
     *
     * @return StripeWebhook
     *
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

        return StripeWebhook::fromJSON($event);
    }

    private function setApiKey()
    {
        $key = config('services.stripe.secret');
        if (empty($key)) {
            throw new \Exception('No Stripe API secret set');
        }
        Stripe::setApiKey($key);
    }
}
