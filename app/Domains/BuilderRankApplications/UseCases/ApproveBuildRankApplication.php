<?php

namespace App\Domains\BuilderRankApplications\UseCases;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppApprovedNotification;
use App\Models\BuilderRankApplication;
use App\Models\Group;

class ApproveBuildRankApplication
{
    public function execute(
        int $applicationId,
        int $promoteGroupId,
    ): BuilderRankApplication {
        $promoteGroup = Group::find($promoteGroupId);

        $application = BuilderRankApplication::find($applicationId);
        $application->status = ApplicationStatus::APPROVED->value;
        $application->closed_at = now();
        $application->save();

        $application->account->groups()->attach($promoteGroup->getKey());

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
