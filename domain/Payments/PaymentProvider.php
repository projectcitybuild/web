<?php

namespace Domain\Payments;

use Domain\Payments\Adapters\StripePaymentAdapter;
use Domain\Payments\PaymentAdapter;
use Illuminate\Support\ServiceProvider;

class PaymentProvider extends ServiceProvider
{
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(PaymentAdapter::class, function ($app) {
            return new StripePaymentAdapter(
                config('services.stripe.secret'),
                config('services.stripe.currency')
            );
        });
    }
}
