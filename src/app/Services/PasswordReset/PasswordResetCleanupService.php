<?php
namespace Domains\Services\PasswordReset;

use Entities\Accounts\Repositories\AccountPasswordResetRepository;
use Illuminate\Support\Facades\Log;

class PasswordResetCleanupService
{
    const DAY_THRESHOLD = 14;

    /**
     * @var AccountPasswordResetRepository
     */
    private $passwordResetRepository;

    public function __construct(AccountPasswordResetRepository $passwordResetRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
    }

    public function cleanup()
    {
        Log::info('Running password reset cleanup service...');

        $thresholdDate = now()->subDays(self::DAY_THRESHOLD);
        $this->passwordResetRepository->deleteOlderThan($thresholdDate);
    }
}
