<?php

namespace App\Library\Stripe;

use App\Enum;

final class StripeWebhookEvent extends Enum
{
    public const CheckoutSessionCompleted = 'checkout.session.completed';
}
