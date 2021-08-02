<?php

namespace Domain\Payments\Adapters;

use App\Library\Stripe\Stripe;
use App\Library\Stripe\StripePaymentMode;
use App\Library\Stripe\StripePaymentType;
use Domain\Payments\PaymentAdapter;

final class StripePaymentAdapter implements PaymentAdapter
{
    private Stripe $stripe;

    public function __construct(Stripe $stripe)
    {
        $this->stripe = $stripe;
    }

    public function createCheckoutSession(string $uniqueSessionId, string $productId, int $quantity = 1): string
    {
        $successRedirectURL = route('front.donate.success');
        $cancelRedirectURL = route('front.donate');

        $redirectURL = $this->stripe->createCheckoutSession(
            $uniqueSessionId,
            $productId,
            1,
            [
                StripePaymentType::fromRawValue(StripePaymentType::CARD),
            ],
            StripePaymentMode::fromRawValue(StripePaymentMode::ONE_TIME), // TODO
            $successRedirectURL,
            $cancelRedirectURL
        );

        return $redirectURL;
    }
}
