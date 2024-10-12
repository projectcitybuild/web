<?php

namespace App\Domains\Donations\UseCases;

use App\Domains\Donations\Exceptions\StripeProductNotFoundException;
use App\Models\Account;
use App\Models\StripeProduct;
use Laravel\Cashier\Checkout;
use Repositories\StripeProductRepository;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

final class BeginCheckout
{
    public function __construct(
        private readonly StripeClient $stripeClient,
    ) {}

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

        $product = StripeProduct::where('product_id', $productId)
            ->where('price_id', $priceId)
            ->first()
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
