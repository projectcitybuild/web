<?php
namespace App\Modules\Donations\Services;

use App\Modules\Donations\Repositories\DonationRepository;
use Carbon\Carbon;

class DonationStatsService
{

    /**
     * @var DonationRepository
     */
    private $donationRepository;

    public function __construct(DonationRepository $donationRepository)
    {
        $this->donationRepository = $donationRepository;
    }

    /**
     * Returns an array of stats related to the overall annual donation percentage
     *
     * @return array
     */
    public function getAnnualPercentageStats() : array
    {
        $annualSum = $this->donationRepository->getAnnualSum();
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
