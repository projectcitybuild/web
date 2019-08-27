<?php
namespace App\Services\Registration;

use App\Entities\Accounts\Repositories\AccountRepository;
use Illuminate\Support\Facades\Log;

class UnactivatedAccountCleanupService
{
    private  $DAY_THRESHOLD;

    /**
     * @var AccountRepository
     */
    private $repository;

    public function __construct(AccountRepository $repository)
    {
        $this->repository = $repository;
        $this->DAY_THRESHOLD = config('auth.unactivated_cleanup_days');
    }

    public function cleanup()
    {
        Log::info('Running unactivated account cleanup service...');

        $thresholdDate = now()->subDays($this->DAY_THRESHOLD);
        $this->repository->deleteOlderThan($thresholdDate);
    }
}
