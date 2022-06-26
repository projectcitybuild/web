<?php

namespace Domain\Donations;

use Domain\Donations\UseCases\DeactivateExpiredDonorPerksUseCase;
use Domain\Donations\UseCases\ProcessPaymentUseCase;
use Entities\Models\Eloquent\Group;
use Illuminate\Support\ServiceProvider;
use Repositories\DonationPerkRepository;
use Repositories\DonationRepository;
use Repositories\PaymentRepository;
use Repositories\StripeProductRepository;
use Shared\Groups\GroupsManager;

class DonationsProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DeactivateExpiredDonorPerksUseCase::class, function ($app) {
            return new DeactivateExpiredDonorPerksUseCase(
                groupsManager: $app->make(GroupsManager::class),
                donationPerkRepository: $app->make(DonationPerkRepository::class),
                donorGroup: Group::where('name', Group::DONOR_GROUP_NAME)->first()
                    ?? throw new \Exception('Could not find donor group'),
            );
        });

        $this->app->bind(ProcessPaymentUseCase::class, function ($app) {
            return new ProcessPaymentUseCase(
                groupsManager: $app->make(GroupsManager::class),
                paymentRepository: $app->make(PaymentRepository::class),
                donationPerkRepository: $app->make(DonationPerkRepository::class),
                donationRepository: $app->make(DonationRepository::class),
                stripeProductRepository: $app->make(StripeProductRepository::class),
                donorGroup: Group::where('name', Group::DONOR_GROUP_NAME)->first()
                    ?? throw new \Exception('Could not find donor group'),
            );
        });
    }
}
