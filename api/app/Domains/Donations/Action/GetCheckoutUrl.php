<?php

namespace App\Domains\Donations\Action;

use App\Domains\Donations\CheckoutCharge;
use App\Domains\Donations\OneTimeCheckoutCharge;
use App\Domains\Donations\SubscriptionCheckoutCharge;
use App\Models\Account;

class GetCheckoutUrl
{
    public function call(
        Account $account,
        CheckoutCharge $charge,
    ): string
    {
        // TODO: whitelist redirect urls

        $options = [
            'success_url' => $charge->redirectSuccessUrl,
            'cancel_url' => $charge->redirectCancelUrl,
            'metadata' => ['purpose' => 'donation'],
        ];

        if ($charge instanceof OneTimeCheckoutCharge) {
            $product = [$charge->priceId => $charge->months];
            $checkout = $account
                ->checkout($product, $options)
                ->toArray(); // Prevent automatic redirection to Stripe Checkout
        } else if ($charge instanceof SubscriptionCheckoutCharge) {
            $checkout = $account
                ->newSubscription($charge->productName, $charge->priceId)
                ->checkout($options)
                ->toArray(); // Prevent automatic redirection to Stripe Checkout
        } else {
            throw new \Exception("Unsupported checkout type");
        }

        return $checkout['url'];
    }
}
