<?php

namespace App\Library\Stripe;

use App\Enum;

final class StripePaymentType extends Enum
{
    public const CARD = 'card';
}
