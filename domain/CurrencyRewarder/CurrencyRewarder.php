<?php

namespace Domain\CurrencyRewarder;

use Entities\Models\Eloquent\DonationPerk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Repositories\BalanceHistoryRepository;
use function now;

final class CurrencyRewarder
{
    public function __construct(
        private BalanceHistoryRepository $balanceHistoryRepository,
    ) {}

    private function isAlreadyRewarded(DonationPerk $perk): bool
    {
        if ($perk->last_currency_reward_at === null) {
            return false;
        }
        $thresholdDate = now()->addWeeks(-1);
        return $perk->last_currency_reward_at->gt($thresholdDate);
    }

    /**
     * @throws \Exception if donation tier reward amount is >= 0
     */
    public function rewardTo(DonationPerk $perk)
    {
        if ($this->isAlreadyRewarded($perk)) {
            Log::info('Currency already rewarded. Skipping...', ['donation_perk_id' => $perk->getKey()]);
            return;
        }

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
    }
}
