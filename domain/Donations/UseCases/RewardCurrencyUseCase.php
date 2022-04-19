<?php

namespace Domain\Donations\UseCases;

use Domain\Balances\Repositories\BalanceHistoryRepository;
use Domain\Donations\Repositories\DonationPerkRepository;
use Entities\Models\Eloquent\DonationPerk;
use Illuminate\Support\Facades\DB;

final class RewardCurrencyUseCase
{
    public function __construct(
        private DonationPerkRepository $donationPerkRepository,
        private BalanceHistoryRepository $balanceHistoryRepository,
    ) {}

    /**
     * @throws \Exception if donation tier reward amount is >= 0
     */
    public function execute()
    {
        $this->donationPerkRepository
            ->getUnrewarded(since: now()->addWeeks(-1))
            ->filter(fn ($dt) => $dt->donationTier !== null && $dt->account !== null)
            ->each(function (DonationPerk $perk) {
                $donationTier = $perk->donationTier;
                $account = $perk->account;

                $rewardAmount = $donationTier->currency_reward;
                if ($rewardAmount <= 0) {
                    throw new \Exception('Donation tier reward amount must be greater than 0');
                }

                DB::beginTransaction();
                try {
                    $newBalance = $account->balance + $rewardAmount;

                    $this->balanceHistoryRepository->create(
                        accountId: $account->getKey(),
                        balanceBefore: $account->balance,
                        balanceAfter: $newBalance,
                        transactionAmount: $rewardAmount,
                        reason: 'Donation tier reward',
                    );

                    $account->balance = $newBalance;
                    $account->save();

                    $perk->last_currency_reward_at = now();
                    $perk->save();

                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            });
    }
}
