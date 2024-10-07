<?php

namespace App\Domains\BuilderRankApplications\UseCases;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppDeclinedNotification;
use App\Models\BuilderRankApplication;

class DenyBuildRankApplication
{
    public function execute(
        int $applicationId,
        string $denyReason,
    ): BuilderRankApplication {
        $application = BuilderRankApplication::find($applicationId);
        $application->status = ApplicationStatus::DENIED->value;
        $application->denied_reason = $denyReason;
        $application->closed_at = now();
        $application->save();

        $application->account->notify(new BuilderRankAppDeclinedNotification($application));

        activity()
            ->on($application)
            ->log('denied');

        return $application;
    }
}
