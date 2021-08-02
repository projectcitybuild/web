<?php

namespace App\Library\Stripe;

use App\Enum;

final class StripePaymentMode extends Enum
{
    public const ONE_TIME = 'payment';
    public const SUBSCRIPTION = 'subscription';
}
