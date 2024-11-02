<?php

namespace App\Http\Controllers\Manage;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\UseCases\ApproveBuildRankApplication;
use App\Domains\BuilderRankApplications\UseCases\DenyBuildRankApplication;
use App\Http\Controllers\WebController;
use App\Models\BuilderRankApplication;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BuilderRanksController extends WebController
{
    public function index(
        Request $request,
    ) {
        $applications = BuilderRankApplication::orderbyRaw('FIELD(status, '.ApplicationStatus::IN_PROGRESS->value.') DESC')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('manage.pages.builder-rank.index')
            ->with(compact('applications'));
    }

    public function show(
        Request $request,
        int $applicationId,
    ) {
        $application = BuilderRankApplication::find($applicationId);
        $buildGroups = Group::where('is_build', true)->get();

        return view('manage.pages.builder-rank.show')
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
