<?php
namespace Application\Modules\Donations\Repositories;

use Application\Modules\Donations\Models\Donation;
use Illuminate\Database\Eloquent\Collection;
use Application\Repository;

class DonationRepository extends Repository
{
    protected $model = Donation::class;

    public function getAll() : Collection
    {
        return $this->getModel()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Gets the total donated for the given year. Uses the
     * current year if no year given
     *
     * @param int $year
     * @return float
     */
    public function getAnnualSum(int $year = null) : float
    {
        $year = $year ?: date('Y');

        return $this->getModel()
            ->whereYear('created_at', $year)
            ->sum('amount');
    }

    public function getAnnualAverage(int $year = null) : float
    {
        $year = $year ?: date('Y');

        return $this->getModel()
            ->whereYear('created_at', $year)
            ->avg('amount');
    }

    public function getAnnualCount(int $year = null) : int
    {
        $year = $year ?: date('Y');

        return $this->getModel()
            ->whereYear('created_at', $year)
            ->count();
    }
}
