<?php

namespace App\Domains\Donations\UseCases;

use App\Core\Domains\Payment\Checkout;
use App\Core\Domains\Payment\Data\Stripe\StripePrice;
use App\Models\Account;
use App\Models\StripeProduct;
use Laravel\Cashier\Checkout as CashierCheckout;
use Stripe\StripeClient;

final class BeginDonationCheckout
{
    public function __construct(
        private readonly StripeClient $stripeClient,
        private readonly Checkout $checkout,
    ) {}

    public function execute(
        Account $account,
        string $priceId,
        ?int $numberOfMonthsToBuy,
    ): CashierCheckout {
        $price = StripePrice::fromPrice(
            $this->stripeClient->prices->retrieve($priceId),
        );
        $product = StripeProduct::where('product_id', $price->productId)
            ->where('price_id', $price->id)
            ->first();
        throw_if($product === null, 'StripeProduct ['.$price->productId.'] not found');

        $donationTier = $product->donationTier;
        throw_if($donationTier === null, 'Cannot checkout a StripeProduct with an unassigned donation tier');

        $successUrl = route('front.donate.success');
        $cancelUrl = route('front.donate');

        if ($price->paymentType->isSubscription()) {
            return $this->checkout->subscription(
                account: $account,
                productName: $donationTier->name,
                priceId: $price->id,
                successRoute: $successUrl,
                cancelRoute: $cancelUrl,
            );
        } else {
            return $this->checkout->oneTime(
                account: $account,
                priceId: $price->id,
                successRoute: $successUrl,
                cancelRoute: $cancelUrl,
                quantity: $numberOfMonthsToBuy,
            );
        }
    }
}
