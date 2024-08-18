<?php

namespace Domain\Donations;

use App\Models\Group;
use Domain\Donations\UseCases\DeactivateExpiredDonorPerks;
use Domain\Donations\UseCases\ProcessPayment;
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
        $this->app->bind(DeactivateExpiredDonorPerks::class, function ($app) {
            return new DeactivateExpiredDonorPerks(
                groupsManager: $app->make(GroupsManager::class),
                donationPerkRepository: $app->make(DonationPerkRepository::class),
                donorGroup: Group::where('name', Group::DONOR_GROUP_NAME)->first()
                    ?? throw new \Exception('Could not find donor group'),
            );
        });

        $this->app->bind(ProcessPayment::class, function ($app) {
            return new ProcessPayment(
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
