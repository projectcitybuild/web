<?php

namespace App\Domains\Donations\UseCases;

use App\Domains\Donations\Data\AnnualDonations;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Money\Money;

class GetAnnualDonationStats
{
    const CACHE_KEY = 'donation_annual_stats';

    public function get(): AnnualDonations
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => $this->calculate());
    }

    private function calculate(): AnnualDonations
    {
        $now = now();
        $donations = Donation::with('payment')
            ->whereHas('payment')
            ->whereYear('created_at', $now->year)
            ->get();

        $amountThisYear = 0;

        foreach ($donations as $donation) {
            $payment = $donation->payment;
            $amountThisYear += $payment->original_unit_amount * $payment->unit_quantity;
        }

        $targetAmount = config('donations.target_funding') * 100;
        $moneyRemaining = $this->money(max(0, $targetAmount - $amountThisYear));
        $moneyThisYear = $this->money($amountThisYear);

        return new AnnualDonations(
            fundingGoalAmount: $this->money($targetAmount)->getAmount(),
            remainingAmountToReachGoal: $moneyRemaining->getAmount(),
            amountRaisedThisYear: $moneyThisYear->getAmount(),
            donationCountThisYear: $donations->count(),
        );
    }

    private function money(?int $dollars): Money
    {
        if ($dollars === null) {
            return Money::USD(0);
        }
        return Money::USD((int)round($dollars / 100));
    }
}
