<?php

namespace Domain\Donations;

use App\Entities\Models\Eloquent\Group;
use Domain\Donations\Repositories\DonationPerkRepository;
use Domain\Donations\UseCases\DeactivateExpiredDonorPerksUseCase;
use Illuminate\Support\ServiceProvider;
use Shared\Groups\GroupsManager;

class DonationsProvider extends ServiceProvider
{
    /**
     * Register any other events for your application.
     */
    public function boot(): void
    {
        $this->app->bind(DeactivateExpiredDonorPerksUseCase::class, function ($app) {
            return new DeactivateExpiredDonorPerksUseCase(
                groupsManager: $app->make(GroupsManager::class),
                donationPerkRepository: $app->make(DonationPerkRepository::class),
                donorGroup: Group::where('name', Group::DONOR_GROUP_NAME)->first()
                    ?? throw new \Exception("Could not find donor group"),
            );
        });
    }
}
