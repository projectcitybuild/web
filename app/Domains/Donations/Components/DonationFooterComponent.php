<?php

namespace App\Domains\Donations\Components;

use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\View\Component;
use Money\Money;

class DonationFooterComponent extends Component
{
    public function render()
    {
        $requiredAmount = Money::USD(config('donations.target_funding') * 100);

        $now = now();
        $thisYear = $now->year;
        $lastDayOfThisYear = new Carbon('last day of december');

        $totalDonationsThisYear = Money::USD(Donation::whereYear('created_at', $thisYear)->sum('amount') ?? 0);
        $remainingDaysThisYear = $now->diff($lastDayOfThisYear)->totalDays;

        $lastYear = $now->subYear()->year;
        $totalDonationsLastYear = Money::USD(Donation::whereYear('created_at', $lastYear)->sum('amount') ?? 0);

        return view('front.components.donation-footer', [
            'donations' => [
                'raised_last_year' => $totalDonationsLastYear->getAmount() ?: 0,
                'remaining_days' => floor($remainingDaysThisYear),
                'still_required' => number_format($requiredAmount->subtract($totalDonationsThisYear)->getAmount() / 100, 2),
            ],
        ]);
    }
}
