<?php

namespace App\Services\PasswordReset;

use App\Entities\Accounts\Repositories\AccountPasswordResetRepository;
use Illuminate\Support\Facades\Log;

class PasswordResetCleanupService
{
    const DAY_THRESHOLD = 14;

    private AccountPasswordResetRepository $passwordResetRepository;

    public function __construct(AccountPasswordResetRepository $passwordResetRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
    }

    public function cleanup(): void
    {
        Log::info('Running password reset cleanup service...');

        $thresholdDate = now()->subDays(self::DAY_THRESHOLD);
        $this->passwordResetRepository->deleteOlderThan($thresholdDate);
    }
}
