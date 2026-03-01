<?php

namespace App\Core\Domains\Payment\UseCases;

use App\Models\Payment;
use Laravel\Cashier\Cashier;
use Money\Money;

final class RecordPayment
{
    public function save(
        string $customerId,
        string $productId,
        string $priceId,
        Money $paidUnitAmount,
        Money $originalUnitAmount,
        int $unitQuantity,
    ): Payment {
        abort_if(
            $paidUnitAmount->isZero() || $paidUnitAmount->isNegative(),
            code: 400,
            message: 'Paid amount was zero or less',
        );
        abort_if(
            $originalUnitAmount->isZero() || $originalUnitAmount->isNegative(),
            code: 400,
            message: 'Expected amount was zero or less',
        );
        abort_if(
            $unitQuantity <= 0,
            code: 400,
            message: 'Quantity purchased was zero',
        );

        $account = Cashier::findBillable($customerId);

        return Payment::create([
            'account_id' => $account?->id,
            'stripe_product' => $productId,
            'stripe_price' => $priceId,
            'paid_currency' => $paidUnitAmount->getCurrency()->getCode(),
            'paid_unit_amount' => $paidUnitAmount->getAmount(),
            'original_currency' => $originalUnitAmount->getCurrency(),
            'original_unit_amount' => $originalUnitAmount->getAmount(),
            'unit_quantity' => $unitQuantity,
        ]);
    }
}
