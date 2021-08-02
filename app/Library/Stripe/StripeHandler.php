<?php

namespace App\Library\Stripe;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeHandler
{
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
}
