<?php

namespace App\Domains\BuilderRankApplications\UseCases;

use App\Core\Domains\Auditing\AuditEventRecorder;
use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppApprovedNotification;
use App\Models\BuilderRankApplication;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class ApproveBuildRankApplication
{
    public function __construct(
        private AuditEventRecorder $auditEventRecorder,
    ) {}

    public function execute(
        int $applicationId,
        int $promoteRoleId,
    ): BuilderRankApplication {
        $application = BuilderRankApplication::find($applicationId);
        $promoteRole = Role::find($promoteRoleId);

        abort_if($application->isReviewed(), 409);

        $oldStatus = $application->getRawOriginal('status');

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

            $application->account->auditSync('roles', $updatedRoleIds, true, ['roles.id', 'roles.name']);
        });

        $application->account->notify(
            new BuilderRankAppApprovedNotification(
                builderRankApplication: $application,
                rolePromotedTo: $promoteRole,
            )
        );

        $this->auditEventRecorder->record(
            subject: $application,
            event: 'approved',
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => ApplicationStatus::APPROVED->value],
        );

        return $application;
    }
}
