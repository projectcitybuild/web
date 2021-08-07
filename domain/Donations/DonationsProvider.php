<?php

namespace Domain\Donations;

use Domain\Donations\Adapters\StripePaymentAdapter;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class DonationsProvider extends ServiceProvider
{
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(DonationService::class, function ($app) {
            $stripeClient = new StripeClient(config('services.stripe.secret'));
            return new DonationService($stripeClient);
        });
    }
}
