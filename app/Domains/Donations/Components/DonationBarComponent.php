<?php

namespace App\Domains\Donations\Components;

use App\Domains\Donations\UseCases\GetAnnualDonationStats;
use Illuminate\View\Component;

class DonationBarComponent extends Component
{
    public function __construct(
        private readonly GetAnnualDonationStats $getAnnualDonationStats,
    ) {}

    public function render()
    {
        $stats = $this->getAnnualDonationStats->get();

        $percentage = $stats->raisedThisYear / $stats->amountRequired;
        $percentage = max(0, min(1, $percentage));

        return view('front.components.donation-bar', [
            'current' => $stats->raisedThisYear,
            'percentage' => $percentage * 100,
            'indicators' => $this->indicators(target: $stats->amountRequired),
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
