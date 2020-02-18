<?php

namespace App\Library\Stripe;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Webhook;

class StripeHandler
{
    private $currency = 'usd';

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
     * @param int $amountInCents
     * $param ?string $email            If provided, prefills the 'customer email' field on the checkout page
     * @return string Stripe session ID
     */
    public function createOneTimeCheckoutSession(string $uniqueSessionId, int $amountInCents, ?string $email = null): string
    {
        $sessionData = [
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
        ];

        if ($email !== null) {
            $sessionData['customer_email'] = $email;
        }

        $session = Session::create($sessionData);

        return $session->id;
    }

    public function createRecurringCheckoutSession(string $uniqueSessionId, int $amountInCents): string
    {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'client_reference_id' => $uniqueSessionId,
            'subscription_data' => [
                'items' => [[
                    'plan' => 'plan_Gl1KcR6J405bpG',
                    'quantity' => round($amountInCents / 100),
                ]],
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
        return StripeWebhook::fromJSON($event);
    }
}