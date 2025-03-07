<?php

namespace App\Core\Domains\Payment;

use App\Core\Domains\Payment\Listeners\StripeEventListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Events\WebhookReceived;

final class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Event::listen(
            WebhookReceived::class,
            StripeEventListener::class,
        );
    }
}
