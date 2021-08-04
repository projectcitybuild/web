<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Payments\Models\Payment;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

final class StripeWebhookController extends CashierController
{
    /**
     * Handle checkout complete events and fulfil the payment.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleCheckoutSessionCompleted(array $payload)
    {
        Log::info('[webhook] invoice.payment_succeeded', ['payload' => $payload]);

        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        $object = $payload['data']['object'];
        $firstLine = $object->lines->data[0];

//        Payment::create([
//            'account_id' => $user !== null ? $user->getKey() : null,
//            'stripe_price' => $firstLine->price->id,
//            'stripe_product' => ,
//            'amount_paid_in_cents' => $firstLine->amount,
//            'quantity' => ,
//            'is_subscription_payment' => true,
//        ]);

        return $this->successMethod();
    }
}
