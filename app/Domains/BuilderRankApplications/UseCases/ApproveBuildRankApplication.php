<?php

namespace App\Domains\BuilderRankApplications\UseCases;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppApprovedNotification;
use App\Models\BuilderRankApplication;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class ApproveBuildRankApplication
{
    public function execute(
        int $applicationId,
        int $promoteRoleId,
    ): BuilderRankApplication {
        $application = BuilderRankApplication::find($applicationId);
        $promoteRole = Role::find($promoteRoleId);

        abort_if($application->isReviewed(), 409);

        DB::transaction(function () use ($application, $promoteRole) {
            $application->status = ApplicationStatus::APPROVED->value;
            $application->closed_at = now();
            $application->save();

            $updatedRoleIds = $application
                ->account
                ->roles()
                ->where('role_type', '!=', 'build')
                ->get()
                ->push($promoteRole);

            $application->account->roles()->sync($updatedRoleIds);
        });

        $application->account->notify(
            new BuilderRankAppApprovedNotification(
                builderRankApplication: $application,
                rolePromotedTo: $promoteRole,
            )
        );

        activity()
            ->on($application)
            ->log('approved');

        return $application;
    }
}
