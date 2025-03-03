<?php

namespace App\Domains\Donations;

use App\Domains\Donations\Components\DonationBarComponent;
use App\Domains\Donations\Listeners\StripeEventListener;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Events\WebhookReceived;

final class DonationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Blade::component('donation-bar', DonationBarComponent::class);

        Event::listen(
            WebhookReceived::class,
            StripeEventListener::class,
        );
    }
}
