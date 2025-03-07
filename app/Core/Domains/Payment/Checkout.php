<?php

namespace App\Core\Domains\Payment;

use Laravel\Cashier\Billable;
use Laravel\Cashier\Checkout as CashierCheckout;

final class Checkout
{
    public function subscription(
        Billable $billable,
        String $productName,
        string $priceId,
        string $successRoute,
        string $cancelRoute,
    ): CashierCheckout {
        $redirects = [
            'success_url' => $successRoute,
            'cancel_url' => $cancelRoute,
        ];
        return $billable
            ->newSubscription($productName, $priceId)
            ->checkout($redirects);
    }

    public function oneTime(
        Billable $billable,
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

        return $billable->checkout($item, $redirects);
    }
}
