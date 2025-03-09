<?php

namespace App\Core\Domains\Payment;

use App\Models\Account;
use Laravel\Cashier\Checkout as CashierCheckout;

final class Checkout
{
    public function subscription(
        Account $account,
        string $productName,
        string $priceId,
        string $successRoute,
        string $cancelRoute,
    ): CashierCheckout {
        $redirects = [
            'success_url' => $successRoute,
            'cancel_url' => $cancelRoute,
        ];
        return $account
            ->newSubscription($productName, $priceId)
            ->checkout($redirects);
    }

    public function oneTime(
        Account $account,
        string $priceId,
        string $successRoute,
        string $cancelRoute,
        int $quantity,
    ): CashierCheckout {
        $redirects = [
            'success_url' => $successRoute,
            'cancel_url' => $cancelRoute,
        ];
        $item = [$priceId => $quantity];

        return $account->checkout($item, $redirects);
    }
}
