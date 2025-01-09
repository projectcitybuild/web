<?php

namespace App\Http\Controllers\Review\BuilderRanks;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\UseCases\ApproveBuildRankApplication;
use App\Domains\BuilderRankApplications\UseCases\DenyBuildRankApplication;
use App\Http\Controllers\WebController;
use App\Models\BuilderRankApplication;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BuilderRanksController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', BuilderRankApplication::class);

        $applications = BuilderRankApplication::orderbyRaw('FIELD(status, '.ApplicationStatus::IN_PROGRESS->value.') DESC')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('manage.pages.builder-rank.index')
            ->with(compact('applications'));
    }

    public function show(Request $request, BuilderRankApplication $application)
    {
        Gate::authorize('view', $application);

        $buildGroups = Group::whereBuildType()->get();

        return view('manage.pages.builder-rank.show')
            ->with(compact('application', 'buildGroups'));
    }

    public function approve(
        Request $request,
        BuilderRankApplication $application,
        ApproveBuildRankApplication $approveBuildRankApplication,
    ) {
        Gate::authorize('update', $application);

        $allowedGroups = Group::whereBuildType()->get()
            ->map(fn ($group) => $group->getKey())
            ->toArray();

        $validated = $request->validate([
            'promote_group' => ['required', Rule::in($allowedGroups)],
        ]);

        $application = $approveBuildRankApplication->execute(
            applicationId: $application->getKey(),
            promoteGroupId: $validated['promote_group'],
        );

        return redirect()->action(
            action: [BuilderRanksController::class, 'show'],
            parameters: $application->getKey(),
        );
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

        return redirect()->action(
            action: [BuilderRanksController::class, 'show'],
            parameters: $application->getKey(),
        );
    }
}
