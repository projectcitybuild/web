<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\ApiController;
use App\Library\Stripe\Stripe;
use App\Library\Stripe\StripeWebhookEvent;
use Domain\Payments\DonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class WebhookController extends ApiController
{
    public function stripe(Request $request, Stripe $stripe, DonationService $donationService)
    {
        $payload = $request->getContent();
        $signature = $request->headers->get('Stripe-Signature');

        Log::debug('Received Stripe webhook', [
            'payload' => $payload,
            'signature' => $signature,
        ]);

        $payload = $stripe->parseWebhookPayload($payload, $signature);

        if ($payload === null) {
            Log::debug('Unsupported or malformed Stripe webhook payload');

            return response()->json(null, 204);
        }

        switch ($payload->event) {
            case StripeWebhookEvent::CheckoutSessionCompleted:
                Log::info('Webhook acknowledged ['.$payload->event.']');
                $donationService->processDonation(
                    $payload->sessionId,
                    $payload->transactionId,
                    $payload->amountPaidInCents,
                );

                return response()->json(null, 200);

            default:
                Log::info('Webhook ignored ['.$payload->event.']');

                return response()->json(null, 204);
        }
    }
}
