<?php

namespace Domain\BuilderRankApplications\UseCases;

use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\Group;
use Entities\Notifications\BuilderRankAppApprovedNotification;
use Repositories\BuilderRankApplicationRepository;
use Shared\Groups\GroupsManager;

class ApproveBuildRankApplication
{
    public function __construct(
        private readonly GroupsManager $groupsManager,
        private readonly BuilderRankApplicationRepository $applicationRepository,
    ) {
    }

    public function execute(
        int $applicationId,
        int $promoteGroupId,
    ): BuilderRankApplication {
        $promoteGroup = Group::find($promoteGroupId);
        $application = $this->applicationRepository->first(applicationId: $applicationId);

        $this->applicationRepository->approve(application: $application);

        $this->groupsManager->addMember(group: $promoteGroup, account: $application->account);

        $application->account->notify(
            new BuilderRankAppApprovedNotification(
                builderRankApplication: $application,
                groupPromotedTo: $promoteGroup,
            )
        );

        activity()
            ->on($application)
            ->log('approved');

        return $application;
    }
}
