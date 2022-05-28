<?php

namespace Domain\BuilderRankApplications\UseCases;

use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\Group;
use Entities\Notifications\BuilderRankAppApprovedNotification;
use Entities\Repositories\BuilderRankApplicationRepository;
use Shared\Groups\GroupsManager;

class ApproveBuildRankApplicationUseCase
{
    public function __construct(
        private GroupsManager $groupsManager,
        private BuilderRankApplicationRepository $applicationRepository,
    ) {}

    public function execute(
        int $applicationId,
        Group $promoteGroup,
    ): BuilderRankApplication {
        $application = $this->applicationRepository->first(applicationId: $applicationId);

        $this->applicationRepository->approve(application: $application);

        $this->groupsManager->addMember(group: $promoteGroup, account: $application->account);

        $application->account->notify(
            new BuilderRankAppApprovedNotification(
                builderRankApplication: $application,
                groupPromotedTo: $promoteGroup,
            )
        );

        return $application;
    }
}
