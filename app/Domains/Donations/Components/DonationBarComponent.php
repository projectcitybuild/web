<?php

namespace App\Domains\Donations\Components;

use App\Models\Donation;
use Illuminate\View\Component;
use Money\Money;

class DonationBarComponent extends Component
{
    public function render()
    {
        $requiredAmountInDollars = config('donations.target_funding');
        $requiredAmountInCents = Money::USD($requiredAmountInDollars * 100);

        $donationsThisYearInCents = Donation::whereYear('created_at', now()->year)->sum('amount') ?: 0;
        $donationsThisYearInCents = Money::USD($donationsThisYearInCents);

        $percentage = $donationsThisYearInCents->getAmount() / $requiredAmountInCents->getAmount();
        $percentage = max(0, min(1, $percentage));

        return view('front.components.donation-bar', [
            'current' => $donationsThisYearInCents
                ->divide(100)
                ->getAmount(),
            'percentage' => $percentage * 100,
            'indicators' => $this->indicators(target: $requiredAmountInDollars),
        ]);
    }

    private function indicators(int $target, int $numOfIndicators = 5): array
    {
        $indicators = [];
        for ($i = 0; $i < $numOfIndicators; $i++) {
            $indicators[] = ($target / ($numOfIndicators - 1)) * $i;
        }
        return $indicators;
    }
}
