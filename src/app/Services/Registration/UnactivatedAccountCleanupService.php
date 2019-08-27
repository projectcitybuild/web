<?php
namespace App\Services\Registration;

use App\Entities\Accounts\Repositories\AccountRepository;
use Illuminate\Support\Facades\Log;

class UnactivatedAccountCleanupService
{
    const DAY_THRESHOLD = 14;

    /**
     * @var AccountRepository
     */
    private $repository;

    public function __construct(AccountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function cleanup()
    {
        Log::info('Running unactivated account cleanup service...');

        $thresholdDate = now()->subDays(self::DAY_THRESHOLD);
        $this->repository->deleteUnactivatedOlderThan($thresholdDate);
    }
}
