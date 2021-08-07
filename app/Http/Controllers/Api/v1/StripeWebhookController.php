<?php

namespace App\Http\Controllers\Api\v1;

use Domain\Donations\DonationService;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

final class StripeWebhookController extends CashierController
{
    private DonationService $donationService;

    public function __construct(DonationService $donationService)
    {
        $this->donationService = $donationService;
    }

    /**
     * Handle Checkout complete events and fulfil payments.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleCheckoutSessionCompleted(array $payload)
    {
        Log::info('[webhook] checkout.session_completed', ['payload' => $payload]);

        $customerId = $payload['data']['object']['customer'];
        $account = $this->getUserByStripeId($customerId);

        if ($account === null) {
            Log::warning('Could not find user matching customer id: '.$customerId);

            return $this->successMethod();
        }

        $sessionId = $payload['data']['object']['id'];
        $this->donationService->processPayment($account, $sessionId);

        return $this->successMethod();
    }
}
