<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use Domain\BuilderRankApplications\UseCases\ApproveBuildRankApplication;
use Domain\BuilderRankApplications\UseCases\DenyBuildRankApplication;
use Entities\Models\Eloquent\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Repositories\BuilderRankApplicationRepository;

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
        ApproveBuildRankApplication $approveBuildRankApplication,
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
        DenyBuildRankApplication $denyBuildRankApplication,
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
