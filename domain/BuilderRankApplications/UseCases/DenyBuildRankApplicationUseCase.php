<?php

namespace Domain\BuilderRankApplications\UseCases;

use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Notifications\BuilderRankAppDeclinedNotification;
use Repositories\BuilderRankApplicationRepository;

class DenyBuildRankApplicationUseCase
{
    public function __construct(
        private BuilderRankApplicationRepository $applicationRepository,
    ) {
    }

    public function execute(
        int $applicationId,
        string $denyReason,
    ): BuilderRankApplication {
        $application = $this->applicationRepository->first(applicationId: $applicationId);

        $this->applicationRepository->deny(
            application: $application,
            reason: $denyReason,
        );

        $application->account->notify(new BuilderRankAppDeclinedNotification($application));

        return $application;
    }
}
