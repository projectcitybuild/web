<?php
namespace Domains\Services\Registration;

use App\Entities\Accounts\Repositories\UnactivatedAccountRepository;
use Illuminate\Support\Facades\Log;

class UnactivatedAccountCleanupService
{
    const DAY_THRESHOLD = 14;

    /**
     * @var UnactivatedAccountRepository
     */
    private $repository;

    public function __construct(UnactivatedAccountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function cleanup()
    {
        Log::info('Running unactivated account cleanup service...');

        $thresholdDate = now()->subDays(self::DAY_THRESHOLD);
        $this->repository->deleteOlderThan($thresholdDate);
    }
}
