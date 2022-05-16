<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Models\Eloquent\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Shared\Groups\GroupsManager;

class BuilderRanksController extends WebController
{
    public function index(Request $request)
    {
        $applications = BuilderRankApplication::orderbyRaw("FIELD(status, ".ApplicationStatus::IN_PROGRESS->value.") DESC")
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('admin.builder-rank.index')
            ->with(compact('applications'));
    }

    public function show(Request $request, int $applicationId)
    {
        $application = BuilderRankApplication::find($applicationId);
        $buildGroups = Group::where('is_build', true)->get();

        return view('admin.builder-rank.show')
            ->with(compact('application', 'buildGroups'));
    }

    public function approve(
        Request $request,
        int $applicationId,
        GroupsManager $groupsManager,
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

        $nextGroup = Group::find($request->get('promote_group'));

        $application = BuilderRankApplication::find($applicationId);

        $application->status = ApplicationStatus::APPROVED->value;
        $application->closed_at = now();
        $application->save();

        $groupsManager->addMember(group: $nextGroup, account: $application->account);

        return redirect()
            ->action([BuilderRanksController::class, 'show'], $application->getKey());
    }

    public function deny(Request $request, int $applicationId) {
        $validator = Validator::make($request->all(), [
            'deny_reason' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $application = BuilderRankApplication::find($applicationId);

        $application->status = ApplicationStatus::DENIED->value;
        $application->denied_reason = $request->get('deny_reason');
        $application->closed_at = now();
        $application->save();

        return redirect()
            ->action([BuilderRanksController::class, 'show'], $application->getKey());
    }
}
