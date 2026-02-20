<?php

namespace App\Domains\Donations;

use App\Core\Domains\Payment\Events\PaymentCreated;
use App\Domains\Donations\Components\DonationBarComponent;
use App\Domains\Donations\Components\DonationCardComponent;
use App\Domains\Donations\Listeners\DonationListener;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class DonationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('donation-bar', DonationBarComponent::class);
        Blade::component('donation-card', DonationCardComponent::class);

        Event::listen(
            PaymentCreated::class,
            DonationListener::class,
        );
    }
}
