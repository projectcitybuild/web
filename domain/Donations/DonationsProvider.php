<?php

namespace Domain\Donations;

use App\Entities\Models\Eloquent\Group;
use Domain\Donations\Repositories\DonationPerkRepository;
use Domain\Donations\Repositories\DonationRepository;
use Domain\Donations\Repositories\PaymentRepository;
use Domain\Donations\UseCases\DeactivateExpiredDonorPerksUseCase;
use Domain\Donations\UseCases\ProcessPaymentUseCase;
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

        $this->app->bind(ProcessPaymentUseCase::class, function ($app) {
            return new ProcessPaymentUseCase(
                groupsManager: $app->make(GroupsManager::class),
                paymentRepository: $app->make(PaymentRepository::class),
                donationPerkRepository: $app->make(DonationPerkRepository::class),
                donationRepository: $app->make(DonationRepository::class),
                donorGroup: Group::where('name', Group::DONOR_GROUP_NAME)->first()
                    ?? throw new \Exception("Could not find donor group"),
            );
        });
    }
}
