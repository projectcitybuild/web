<?php

namespace Domain\CurrencyRewarder;

use Domain\CurrencyRewarder\Jobs\RewardCurrencyJob;
use Domain\Donations\Events\DonationPerkCreated;
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
