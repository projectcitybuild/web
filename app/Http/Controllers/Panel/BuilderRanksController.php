<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Domain\BuilderRankApplications\UseCases\ApproveBuildRankApplicationUseCase;
use Domain\BuilderRankApplications\UseCases\DenyBuildRankApplicationUseCase;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\Group;
use Entities\Notifications\BuilderRankAppApprovedNotification;
use Entities\Notifications\BuilderRankAppDeclinedNotification;
use Entities\Repositories\BuilderRankApplicationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Shared\Groups\GroupsManager;

class BuilderRanksController extends WebController
{
    public function index(
        Request $request,
        BuilderRankApplicationRepository $applicationRepository,
    ) {
        $applications = $applicationRepository->allWithPriority(perPage: 100);

        return view('admin.builder-rank.index')
            ->with(compact('applications'));
    }

    public function show(
        Request $request,
        int $applicationId,
        BuilderRankApplicationRepository $applicationRepository,
    ) {
        $application = $applicationRepository->first(applicationId: $applicationId);
        $buildGroups = Group::where('is_build', true)->get();

        return view('admin.builder-rank.show')
            ->with(compact('application', 'buildGroups'));
    }

    public function approve(
        Request $request,
        int $applicationId,
        ApproveBuildRankApplicationUseCase $approveBuildRankApplication,
    ) {
        $allowedGroups = Group::where('is_build', true)->get()
            ->map(fn ($group) => $group->getKey())
            ->toArray();

        $validator = Validator::make($request->all(), [
            'promote_group' => ['required', Rule::in($allowedGroups)],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $application = $approveBuildRankApplication->execute(
            applicationId: $applicationId,
            promoteGroupId: $request->get('promote_group'),
        );

        return redirect()->action(
            action: [BuilderRanksController::class, 'show'],
            parameters: $application->getKey(),
        );
    }

    public function deny(
        Request $request,
        int $applicationId,
        DenyBuildRankApplicationUseCase $denyBuildRankApplication,
    ) {
        $validator = Validator::make($request->all(), [
            'deny_reason' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $application = $denyBuildRankApplication->execute(
            applicationId: $applicationId,
            denyReason: $request->get('deny_reason'),
        );

        return redirect()->action(
            action: [BuilderRanksController::class, 'show'],
            parameters: $application->getKey(),
        );
    }
}
