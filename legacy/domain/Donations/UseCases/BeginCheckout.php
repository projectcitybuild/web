<?php

namespace Domain\Donations\UseCases;

use Domain\Donations\Exceptions\StripeProductNotFoundException;
use Entities\Models\Eloquent\Account;
use Laravel\Cashier\Checkout;
use Repositories\StripeProductRepository;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

final class BeginCheckout
{
    public function __construct(
        private readonly StripeClient $stripeClient,
        private readonly StripeProductRepository $stripeProductRepository,
    ) {
    }

    /**
     * @throws ApiErrorException
     * @throws StripeProductNotFoundException
     */
    public function execute(
        Account $account,
        string $priceId,
        ?int $numberOfMonthsToBuy,
    ): Checkout {
        $price = $this->stripeClient->prices->retrieve($priceId);

        $isSubscription = $price['recurring'] !== null;

        $productId = $price['product'];
        $priceId = $price['id'];

        $product = $this->stripeProductRepository->first(productId: $productId, priceId: $priceId)
            ?? throw new StripeProductNotFoundException();

        $donationTier = $product->donationTier;
        if ($donationTier === null) {
            throw new \Exception('Cannot checkout a StripeProduct with an unassigned donation tier');
        }

        $redirects = [
            'success_url' => route('front.donate.success'),
            'cancel_url' => route('front.donate'),
        ];

        if ($isSubscription) {
            return $account
                ->newSubscription($donationTier->name, $priceId)
                ->checkout($redirects);
        } else {
            return $account
                ->checkout([$priceId => $numberOfMonthsToBuy], $redirects);
        }
    }
}
