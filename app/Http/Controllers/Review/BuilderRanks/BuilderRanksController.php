<?php

namespace App\Http\Controllers\Review\BuilderRanks;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\UseCases\ApproveBuildRankApplication;
use App\Domains\BuilderRankApplications\UseCases\DenyBuildRankApplication;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebReviewPermission;
use App\Http\Controllers\Review\RendersReviewApp;
use App\Http\Controllers\WebController;
use App\Models\BuilderRankApplication;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BuilderRanksController extends WebController
{
    use RendersReviewApp;
    use AuthorizesPermissions;

    public function index(Request $request)
    {
        $this->requires(WebReviewPermission::BUILD_RANK_APPS_VIEW);

        $applications = function () {
            return BuilderRankApplication::orderbyRaw('FIELD(status, '.ApplicationStatus::PENDING->value.') DESC')
                ->with('account.minecraftAccount')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        };

        if ($request->wantsJson()) {
            return $applications();
        }

        return $this->inertiaRender('BuilderRankApplications/BuilderRankApplicationList', [
            'applications' => Inertia::defer($applications),
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

        return $this->inertiaRender(
            'BuilderRankApplications/BuilderRankApplicationShow',
            compact('application', 'buildRoles'),
        );
    }

    public function approve(
        Request $request,
        BuilderRankApplication $application,
        ApproveBuildRankApplication $approveBuildRankApplication,
    ) {
        $this->requires(WebReviewPermission::BUILD_RANK_APPS_DECIDE);

        $allowedRoles = Role::whereBuildType()->get()
            ->map(fn ($role) => $role->getKey())
            ->toArray();

        $validated = $request->validate([
            'promote_role' => ['required', Rule::in($allowedRoles)],
        ]);

        $application = $approveBuildRankApplication->execute(
            applicationId: $application->getKey(),
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
            applicationId: $application->getKey(),
            denyReason: $validated['deny_reason'],
        );

        return to_route('review.builder-ranks.show', $application)
            ->with(['success' => 'Application denied and closed']);
    }
}
