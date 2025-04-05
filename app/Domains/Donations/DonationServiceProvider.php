<?php

namespace App\Domains\Donations;

use App\Core\Domains\Payment\Events\PaymentCreated;
use App\Domains\Donations\Components\DonationBarComponent;
use App\Domains\Donations\Components\DonationFooterComponent;
use App\Domains\Donations\Listeners\DonationListener;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Blade::component('donation-footer', DonationFooterComponent::class);

        Event::listen(
            PaymentCreated::class,
            DonationListener::class,
        );
    }
}
