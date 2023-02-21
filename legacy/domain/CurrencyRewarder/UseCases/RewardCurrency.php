<?php

namespace Domain\CurrencyRewarder\UseCases;

use Domain\CurrencyRewarder\Jobs\RewardCurrencyJob;
use function now;
use Repositories\DonationPerkRepository;

final class RewardCurrency
{
    public function __construct(
        private readonly DonationPerkRepository $donationPerkRepository,
    ) {
    }

    /**
     * @throws \Exception if donation tier reward amount is >= 0
     */
    public function execute()
    {
        $this->donationPerkRepository
            ->getUnrewarded(since: now()->addWeeks(-1))
            ->filter(fn ($dt) => $dt->donationTier !== null && $dt->account !== null)
            ->each(fn ($perk) => RewardCurrencyJob::dispatch($perk));
    }
}
