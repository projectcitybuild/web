<?php

namespace App\Domains\Donations\Components;

use App\Models\Donation;
use Illuminate\View\Component;

class DonationBarComponent extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        $requiredAmount = config('donations.target_funding');

        $thisYear = now()->year;
        $totalDonationsThisYear = Donation::whereYear('created_at', $thisYear)->sum('amount') ?: 0;
        $percentage = round($totalDonationsThisYear / $requiredAmount * 100) ?: 0;

        return view('front.components.donation-bar', [
            'current' => $totalDonationsThisYear,
            'percentage' => max(1, $percentage),
            'indicators' => [0, 250, 500, 750, 1000],
        ]);
    }
}
