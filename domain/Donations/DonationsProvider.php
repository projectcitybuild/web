<?php

namespace Domain\Donations;

use Domain\Donations\Adapters\StripePaymentAdapter;
use Illuminate\Support\ServiceProvider;

class DonationsProvider extends ServiceProvider
{
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
//        $this->app->bind(PaymentAdapter::class, function ($app) {
//            return new StripePaymentAdapter($app->make(Stripe::class));
//        });
        $this->app->bind(DonationService::class, function ($app) {
            return new DonationService(
                $app->make(StripePaymentAdapter::class)
            );
        });
    }
}
