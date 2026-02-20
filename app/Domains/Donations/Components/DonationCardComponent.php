<?php

namespace App\Domains\Donations\Components;

use App\Domains\Donations\UseCases\GetAnnualDonationStats;
use Carbon\Carbon;
use Illuminate\View\Component;

class DonationCardComponent extends Component
{
    public function __construct(
        private readonly GetAnnualDonationStats $getAnnualDonationStats,
    ) {}

    public function render()
    {
        $stats = $this->getAnnualDonationStats->get();

        return view('front.components.donation-card', [
            'stats' => $stats,
            'remaining_days' => $this->remainingDaysThisYear(),
        ]);
    }

    private function remainingDaysThisYear(): int
    {
        $now = now();
        $lastDayOfThisYear = new Carbon('last day of december');
        $remainingDaysThisYear = $now->diff($lastDayOfThisYear)->totalDays;
        return floor($remainingDaysThisYear);
    }
}
