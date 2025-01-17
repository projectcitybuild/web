<?php

namespace App\Domains\Donations;

use App\Domains\Donations\Components\DonationBarComponent;
use Illuminate\Support\Facades\Blade;
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
    }
}
