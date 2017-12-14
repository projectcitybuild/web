<?php
namespace App\Modules\Donations\Repositories;

use App\Modules\Donations\Models\Donation;

class DonationRepository {

    private $donationModel;

    public function __construct(Donation $donationModel) {
        $this->donationModel = $donationModel;
    }

    /**
     * Gets the total donated for the given year. Uses the
     * current year if no year given
     *
     * @param int $year
     * @return float
     */
    public function getAnnualSum(int $year = null) : float {
        $year = $year ?: date('Y');

        return $this->donationModel
            ->whereYear('created_at', $year)
            ->sum('amount');
    }

}