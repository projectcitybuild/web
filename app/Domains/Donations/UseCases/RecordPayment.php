<?php

namespace App\Domains\Donations\UseCases;

use App\Domains\Donations\Data\Amount;
use App\Domains\Donations\Data\Payloads\StripeCheckoutLineItem;
use App\Models\Account;
use App\Models\Payment;

final class RecordPayment
{
    public function saveLineItem(StripeCheckoutLineItem $lineItem, Account $account)
    {
        $this->save(
            account: $account,
            productId: $lineItem->productId,
            priceId: $lineItem->priceId,
            paidAmount: $lineItem->paidAmount,
            originalAmount: $lineItem->originalAmount,
            quantity: $lineItem->quantity,
        );
    }

    public function save(
        Account $account,
        string $productId,
        string $priceId,
        Amount $paidAmount,
        Amount $originalAmount,
        int $quantity,
    ) {
        abort_if(
            $paidAmount->value <= 0 || $originalAmount->value <= 0,
            code: 400,
            message: 'Amount paid was zero',
        );
        abort_if(
            $quantity <= 0,
            code: 400,
            message: 'Quantity purchased was zero',
        );

        Payment::create([
            'account_id' => $account->getKey(),
            'stripe_product' => $productId,
            'stripe_price' => $priceId,
            'paid_currency' => $paidAmount->currency,
            'paid_amount' => $paidAmount->value,
            'original_currency' => $originalAmount->currency,
            'original_amount' => $originalAmount->value,
            'quantity' => $quantity,
        ]);
    }
}
