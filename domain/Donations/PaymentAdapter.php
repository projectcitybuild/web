<?php

namespace Domain\Donations;

interface PaymentAdapter
{
    /**
     * Creates a new session for using Stripe's Checkout flow.
     *
     * @param string $uniqueSessionId A unique UUID that will be sent to Stripe to be stored alongside the
     *                                  session. When Stripe notifies us via WebHook that the payment is processed,
     *                                  they will pass us back the UUID so we can fulfill the purchase.
     *
     * @return string Redirect URL to perform the actual transaction (i.e. Stripe Checkout)
     *
     * // FIXME: replace with platform agnostic exception
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCheckoutSession(string $uniqueSessionId, string $productId, int $quantity = 1): string;
}
