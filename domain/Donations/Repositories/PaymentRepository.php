<?php

namespace Domain\Donations\Repositories;

use App\Entities\Models\Eloquent\Payment;
use Domain\Donations\Entities\PaidAmount;

/**
 * @final
 */
class PaymentRepository
{
    public function create(
        int $accountId,
        string $productId,
        string $priceId,
        PaidAmount $paidAmount,
        int $quantity,
        bool $isSubscription,
    ): Payment {
        return Payment::create([
            'account_id' => $accountId,
            'stripe_product' => $productId,
            'stripe_price' => $priceId,
            'amount_paid_in_cents' => $paidAmount->toCents(),
            'quantity' => $quantity,
            'is_subscription_payment' => $isSubscription,
        ]);
    }
}