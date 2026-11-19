<?php

namespace App\Domains\BuilderRankApplications\UseCases;

use App\Core\Domains\Auditing\AuditEventRecorder;
use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppDeclinedNotification;
use App\Models\BuilderRankApplication;

class DenyBuildRankApplication
{
    public function __construct(
        private AuditEventRecorder $auditEventRecorder,
    ) {}

    public function execute(
        int $applicationId,
        string $denyReason,
    ): BuilderRankApplication {
        $application = BuilderRankApplication::find($applicationId);

        abort_if($application->isReviewed(), 409);

        $oldStatus = $application->getOriginal('status');
        $application->status = ApplicationStatus::DENIED;
        $application->denied_reason = $denyReason;
        $application->closed_at = now();
        $application->save();

        $application->account->notify(new BuilderRankAppDeclinedNotification($application));

        $this->auditEventRecorder->record(
            subject: $application,
            event: 'denied',
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => ApplicationStatus::DENIED->value],
        );

        return $application;
    }
}
