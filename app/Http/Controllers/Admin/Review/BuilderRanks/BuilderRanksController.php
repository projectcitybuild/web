<?php

namespace App\Http\Controllers\Admin\Review\BuilderRanks;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\UseCases\ApproveBuildRankApplication;
use App\Domains\BuilderRankApplications\UseCases\DenyBuildRankApplication;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebReviewPermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminBuilderRankApplicationData;
use App\Http\Data\AdminRoleData;
use App\Models\BuilderRankApplication;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BuilderRanksController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function index(Request $request)
    {
        $this->requires(WebReviewPermission::BUILD_RANK_APPS_VIEW);

        $applications = function () {
            return BuilderRankApplication::orderbyRaw('FIELD(status, ?) DESC', [ApplicationStatus::PENDING->value])
                ->with('account.minecraftAccount')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        };

        if ($request->wantsJson()) {
            return $applications();
        }

        return $this->inertiaRender('BuilderRankApplications/BuilderRankApplicationList', [
            'applications' => Inertia::defer(fn () => $applications()->through(
                fn (BuilderRankApplication $application): array => AdminBuilderRankApplicationData::fromModel($application)->toArray(),
            )),
        ]);
    }

    public function show(Request $request, BuilderRankApplication $application)
    {
        $this->requires(WebReviewPermission::BUILD_RANK_APPS_VIEW);

        $application->load(
            'account.minecraftAccount',
            'account.roles',
        );

        $buildRoles = Role::whereBuildType()->get();

        $buildRoles = $buildRoles->map(
            fn (Role $role): array => AdminRoleData::fromModel($role)->toArray(),
        );

        return $this->inertiaRender(
            'BuilderRankApplications/BuilderRankApplicationShow',
            [
                'application' => AdminBuilderRankApplicationData::fromModel($application),
                'buildRoles' => $buildRoles,
            ],
        );
    }

    public function approve(
        Request $request,
        BuilderRankApplication $application,
        ApproveBuildRankApplication $approveBuildRankApplication,
    ) {
        $this->requires(WebReviewPermission::BUILD_RANK_APPS_DECIDE);

        $allowedRoles = Role::whereBuildType()->get()
            ->map(fn ($role) => $role->id)
            ->toArray();

        $validated = $request->validate([
            'promote_role' => ['required', Rule::in($allowedRoles)],
        ]);

        $application = $approveBuildRankApplication->execute(
            applicationId: $application->id,
            promoteRoleId: $validated['promote_role'],
        );

        return to_route('review.builder-ranks.show', $application)
            ->with(['success' => 'Application approved and closed']);
    }

    public function deny(
        Request $request,
        BuilderRankApplication $application,
        DenyBuildRankApplication $denyBuildRankApplication,
    ) {
        $this->requires(WebReviewPermission::BUILD_RANK_APPS_DECIDE);

        $validated = $request->validate([
            'deny_reason' => 'required',
        ]);

        $application = $denyBuildRankApplication->execute(
            applicationId: $application->id,
            denyReason: $validated['deny_reason'],
        );

        return to_route('review.builder-ranks.show', $application)
            ->with(['success' => 'Application denied and closed']);
    }
}
