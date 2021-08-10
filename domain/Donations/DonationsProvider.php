<?php

namespace Domain\Donations;

use App\Http\Actions\SyncUserToDiscourse;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

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
            $stripeClient = new StripeClient(config('services.stripe.secret'));

            return new DonationService($stripeClient);
        });

        $this->app->bind(DonationGroupSyncService::class, function ($app) {
            return new DonationGroupSyncService(
                $app->make(SyncUserToDiscourse::class)
            );
        });

        $this->app->bind(DeactivateExpiredDonorPerks::class, function ($app) {
            return new DeactivateExpiredDonorPerks(
                $app->make(DonationGroupSyncService::class)
            );
        });
    }
}
