<?php

namespace Domain\Donations\Adapters;

use App\Library\Stripe\Stripe;
use App\Library\Stripe\StripePaymentMode;
use App\Library\Stripe\StripePaymentType;
use Domain\Donations\PaymentAdapter;

final class StripePaymentAdapter implements PaymentAdapter
{
    private Stripe $stripe;

    public function __construct(Stripe $stripe)
    {
        $this->stripe = $stripe;
    }

    public function createCheckoutSession(
        string $uniqueSessionId,
        string $productId,
        int $quantity,
        bool $isSubscription
    ): string {
        $successRedirectURL = route('front.donate.success');
        $cancelRedirectURL = route('front.donate');

        $paymentMode = $isSubscription
            ? StripePaymentMode::fromRawValue(StripePaymentMode::SUBSCRIPTION)
            : StripePaymentMode::fromRawValue(StripePaymentMode::ONE_TIME);

        $redirectURL = $this->stripe->createCheckoutSession(
            $uniqueSessionId,
            $productId,
            $quantity,
            [
                StripePaymentType::fromRawValue(StripePaymentType::CARD),
            ],
            $paymentMode,
            $successRedirectURL,
            $cancelRedirectURL
        );

        return $redirectURL;
    }
}
