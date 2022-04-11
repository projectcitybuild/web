<?php

namespace Domain\PasswordReset\UseCases;

use Domain\PasswordReset\Repositories\AccountPasswordResetRepository;
use function now;

class DeleteExpiredPasswordResetsUseCase
{
    const DAY_THRESHOLD = 1;

    public function __construct(
        private AccountPasswordResetRepository $passwordResetRepository
    ) {}

    public function execute()
    {
        /**
         * TODO: expiry dates should be stored with the password reset model
         * as this currently relies on the job queue never breaking
         */
        $thresholdDate = now()->subDays(self::DAY_THRESHOLD);
        $this->passwordResetRepository->deleteOlderThanOrEqualTo($thresholdDate);
    }
}