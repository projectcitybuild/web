<?php

namespace Domain\BuilderRankApplications\UseCases;

use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\Group;
use Entities\Notifications\BuilderRankAppApprovedNotification;
use Entities\Notifications\BuilderRankAppDeclinedNotification;
use Entities\Repositories\BuilderRankApplicationRepository;
use Shared\Groups\GroupsManager;

class DenyBuildRankApplicationUseCase
{
    public function __construct(
        private BuilderRankApplicationRepository $applicationRepository,
    ) {}

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
