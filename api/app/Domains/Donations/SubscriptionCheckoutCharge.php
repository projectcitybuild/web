<?php

namespace App\Domains\Donations;

class SubscriptionCheckoutCharge extends CheckoutCharge
{
    public function __construct(
        readonly string $priceId,
        readonly string $productName,
        readonly string $redirectSuccessUrl,
        readonly string $redirectCancelUrl,
    ) {
        parent::__construct(
            priceId: $this->priceId,
            redirectSuccessUrl: $this->redirectSuccessUrl,
            redirectCancelUrl: $this->redirectCancelUrl,
        );
    }
}
