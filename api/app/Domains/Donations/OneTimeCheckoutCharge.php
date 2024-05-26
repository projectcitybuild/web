<?php

namespace App\Domains\Donations;

class OneTimeCheckoutCharge extends CheckoutCharge
{
    public function __construct(
        readonly string $priceId,
        readonly int $months,
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
