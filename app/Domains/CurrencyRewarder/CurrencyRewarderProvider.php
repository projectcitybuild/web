<?php

namespace App\Domains\CurrencyRewarder;

use App\Domains\CurrencyRewarder\Jobs\RewardCurrencyJob;
use App\Domains\Donations\Events\DonationPerkCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CurrencyRewarderProvider extends ServiceProvider
{
    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(function (DonationPerkCreated $event) {
            RewardCurrencyJob::dispatch($event->donationPerk);
        });
    }
}
