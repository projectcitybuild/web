<?php
namespace App\Services\Donations;

use App\Entities\Donations\Models\Donation;
use Carbon\Carbon;

class DonationStatsService
{
    /**
     * Returns an array of stats related to the overall annual donation percentage
     *
     * @return array
     */
    public function getAnnualPercentageStats() : array
    {
        $year = date('Y');
        $annualSum = Donation::whereYear('created_at', $year)->sum('amount');
        $percentage = round(($annualSum / 1000) * 100);

        $lastDayOfYear = new Carbon('last day of december');
        $now = Carbon::now();
        $remainingDays = $lastDayOfYear->diff($now)->days;

        return [
            'total'         => $annualSum ?: 0,
            'remainingDays' => $remainingDays,
            'percentage'    => max(1, $percentage) ?: 0,
        ];
    }
}
