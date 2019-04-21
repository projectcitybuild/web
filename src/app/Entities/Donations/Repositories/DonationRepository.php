<?php
namespace App\Entities\Donations\Repositories;

use App\Entities\Donations\Models\Donation;
use Illuminate\Database\Eloquent\Collection;
use Application\Contracts\Repository;
use Carbon\Carbon;

class DonationRepository extends Repository
{
    protected $model = Donation::class;

    public function getAll() : Collection
    {
        return $this->getModel()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(?int $accountId,
                           float $amount,
                           ?Carbon $expiryDate,
                           bool $isLifetime,
                           bool $isActive = true) : Donation
    {
        return $this->getModel()->create([
            'account_id' => $accountId,
            'amount' => $amount,
            'perks_end_at' => $expiryDate,
            'is_lifetime_perks' => $isLifetime,
            'is_active' => $isActive,
        ]);
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

    public function getExpiredDonations() : Collection {
        return $this->getModel()
            ->where('is_active', true)
            ->whereDate('perks_end_at', '<=', now())
            ->get();
    }
}
