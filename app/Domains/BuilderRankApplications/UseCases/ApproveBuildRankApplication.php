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
        $application = BuilderRankApplication::find($applicationId);

        abort_if($application->isReviewed(), 409);

        $application->status = ApplicationStatus::APPROVED->value;
        $application->closed_at = now();
        $application->save();

        $promoteGroup = Group::find($promoteGroupId);
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
