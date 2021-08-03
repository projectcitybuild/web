<?php

namespace App\Library\Stripe;

use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Stripe as StripePackage;

final class Stripe
{
    public function __construct(string $stripeSecret)
    {
        if (empty($stripeSecret)) {
            throw new \Exception('No Stripe API secret set');
        }
        StripePackage::setApiKey($stripeSecret);
    }

    public function parseWebhookPayload(string $payload, string $signature): ?StripeWebhookPayload
    {
        $secret = config('services.stripe.webhook.secret');

        $payloadJSON = Webhook::constructEvent($payload, $signature, $secret);

        // Only parse Webhook events we care about
        $event = null;
        try {
            $event = StripeWebhookEvent::fromRawValue($payloadJSON->type);
        } catch (\Exception $e) {
            return null;
        }

        $transactionId = $payloadJSON->data->object->id;
        $sessionId = $payloadJSON->data->object->client_reference_id;
        $amountPaidInCents = $payloadJSON->data->object->amount_total;

        return new StripeWebhookPayload(
            $event,
            $sessionId,
            $transactionId,
            $amountPaidInCents
        );
    }

    public function createCheckoutSession(
        string $uniqueSessionId,
        string $productId,
        int $quantity,
        array $supportedPaymentTypes,
        StripePaymentMode $paymentMode,
        string $successRedirectURL,
        string $cancelRedirectURL
    ): string {
        $session = Session::create([
            'payment_method_types' => array_map(fn ($it) => $it->valueof(), $supportedPaymentTypes),
            'client_reference_id' => $uniqueSessionId,
            'line_items' => [[
                'price' => $productId, // Stripe's "price id" for a product
                'quantity' => $quantity,
            ]],
            'mode' => $paymentMode->valueOf(),
            'success_url' => $successRedirectURL,
            'cancel_url' => $cancelRedirectURL,
        ]);

        // Return URL to the Stripe Checkout page
        return $session->url;
    }
}
