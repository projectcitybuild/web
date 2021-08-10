<?php

namespace Domain\Donations;

use App\Http\Actions\SyncUserToDiscourse;
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
        $this->app->bind(DonationService::class, function ($app) {
            return new DonationService(
                $app->make(DonationGroupSyncService::class)
            );
        });

        $this->app->bind(DeactivateExpiredDonorPerks::class, function ($app) {
            return new DeactivateExpiredDonorPerks(
                $app->make(DonationGroupSyncService::class)
            );
        });

        $this->app->bind(DonationGroupSyncService::class, function ($app) {
            return new DonationGroupSyncService(
                $app->make(SyncUserToDiscourse::class)
            );
        });
    }
}
