<?php
namespace App\Services\Registration;

use App\Entities\Accounts\Repositories\UnactivatedAccountRepository;
use Illuminate\Support\Facades\Log;

class UnactivatedAccountCleanupService
{
    private  $DAY_THRESHOLD;

    /**
     * @var UnactivatedAccountRepository
     */
    private $repository;

    public function __construct(UnactivatedAccountRepository $repository)
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
