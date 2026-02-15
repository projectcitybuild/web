<?php

namespace App\Http\Controllers\Review\BuilderRanks;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\UseCases\ApproveBuildRankApplication;
use App\Domains\BuilderRankApplications\UseCases\DenyBuildRankApplication;
use App\Http\Controllers\Review\RendersReviewApp;
use App\Http\Controllers\WebController;
use App\Models\BuilderRankApplication;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BuilderRanksController extends WebController
{
    use RendersReviewApp;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', BuilderRankApplication::class);

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
        Gate::authorize('view', $application);

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
        Gate::authorize('update', $application);

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
        Gate::authorize('update', $application);

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
