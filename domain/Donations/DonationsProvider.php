<?php

namespace Domain\Donations;

use App\Http\Actions\SyncUserToDiscourse;
use Domain\Donations\Repositories\DonationPerkRepository;
use Domain\Donations\UseCases\DeactivateExpiredDonorPerksUseCase;
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
        $this->app->bind(DeactivateExpiredDonorPerksUseCase::class, function ($app) {
            return new DeactivateExpiredDonorPerksUseCase(
                groupSyncService: $app->make(DonationGroupSyncService::class),
                donationPerkRepository: $app->make(DonationPerkRepository::class),
            );
        });

        $this->app->bind(DonationGroupSyncService::class, function ($app) {
            return new DonationGroupSyncService(
                $app->make(SyncUserToDiscourse::class)
            );
        });
    }
}
