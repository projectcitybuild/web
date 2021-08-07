<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Models\DonationTier;
use App\Entities\Groups\Models\Group;
use App\Entities\Payments\Models\Payment;
use Domain\Donations\DonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Stripe\StripeClient;

final class StripeWebhookController extends CashierController
{
    private DonationService $donationService;

    public function __construct(DonationService $donationService)
    {
        $this->donationService = $donationService;
    }

    /**
     * Handle Checkout complete events and fulfil payments
     *
     * @param  array  $payload
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
