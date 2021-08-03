<?php

namespace App\Library\Stripe;

use App\Enum;

final class StripeWebhookEvent extends Enum
{
    public const CHECKOUT_SESSION_COMPLETED = 'checkout.session.completed';
    public const PAYMENT_INTENT_SUCCEEDED = 'payment_intent.succeeded';
    public const CUSTOMER_CREATED = 'customer.created';
    public const CUSTOMER_UPDATED = 'customer.updated';
}
