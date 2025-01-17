<?php

namespace App\Domains\BuilderRankApplications\UseCases;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppApprovedNotification;
use App\Models\BuilderRankApplication;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class ApproveBuildRankApplication
{
    public function execute(
        int $applicationId,
        int $promoteGroupId,
    ): BuilderRankApplication {
        $application = BuilderRankApplication::find($applicationId);
        $promoteGroup = Group::find($promoteGroupId);

        abort_if($application->isReviewed(), 409);

        DB::transaction(function () use ($application, $promoteGroup) {
            $application->status = ApplicationStatus::APPROVED->value;
            $application->closed_at = now();
            $application->save();

            $updatedGroupIds = $application
                ->account
                ->groups()
                ->where('group_type', '!=', 'build')
                ->get()
                ->push($promoteGroup);

            $application->account->groups()->sync($updatedGroupIds);
        });

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
