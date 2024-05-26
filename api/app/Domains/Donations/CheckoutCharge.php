<?php

namespace App\Domains\Donations;

/**
 * If only PHP had sealed classes...
 */
abstract class CheckoutCharge
{
    public function __construct(
        readonly string $priceId,
        readonly string $redirectSuccessUrl,
        readonly string $redirectCancelUrl,
    ) {}
}
