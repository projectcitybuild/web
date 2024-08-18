<?php

namespace App\Domains\BuilderRankApplications\UseCases;

use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppDeclinedNotification;
use App\Models\BuilderRankApplication;
use Repositories\BuilderRankApplicationRepository;

class DenyBuildRankApplication
{
    public function __construct(
        private readonly BuilderRankApplicationRepository $applicationRepository,
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

        activity()
            ->on($application)
            ->log('denied');

        return $application;
    }
}
