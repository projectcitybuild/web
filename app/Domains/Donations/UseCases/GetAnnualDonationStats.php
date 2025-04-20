<?php

namespace App\Domains\Donations\UseCases;

use App\Domains\Donations\Data\AnnualDonations;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Money\Money;

class GetAnnualDonationStats
{
    private const CACHE_KEY = 'donation_annual_stats';

    public function get(): AnnualDonations
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => $this->calculate());
    }

    private function calculate(): AnnualDonations
    {
        $now = now();
        $thisYear = $now->year;
        $lastYear = $now->subYear()->year;

        $donations = Donation::where(function ($query) use ($thisYear, $lastYear) {
            $query->whereYear('created_at', $thisYear)
                ->orWhereYear('created_at', $lastYear);
        })->get();

        $amountThisYear = 0;
        $amountLastYear = 0;

        foreach ($donations as $donation) {
            if (Carbon::parse($donation->created_at)->year == $thisYear) {
                $amountThisYear += $donation->amount;
            } else {
                $amountLastYear += $donation->amount;
            }
        }
        $amountThisYear = $this->money($amountThisYear);
        $amountLastYear = $this->money($amountLastYear);
        $amountRequired = $this->money(config('donations.target_funding') * 100);

        return new AnnualDonations(
            amountRequired: $amountRequired->getAmount(),
            raisedThisYear: $amountThisYear->getAmount(),
            raisedLastYear: $amountLastYear->getAmount(),
        );
    }

    private function money(?int $dollars): Money
    {
        if ($dollars === null) {
            return Money::USD(0);
        }
        return Money::USD($dollars / 100);
    }
}
