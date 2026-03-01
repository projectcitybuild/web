<?php

namespace App\Http\Controllers\Front;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Data\BuilderRank;
use App\Domains\BuilderRankApplications\Exceptions\ApplicationAlreadyInProgressException;
use App\Domains\BuilderRankApplications\UseCases\CreateBuildRankApplication;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebReviewPermission;
use App\Http\Controllers\WebController;
use App\Http\Requests\BuilderRankApplicationRequest;
use App\Models\BuilderRankApplication;
use Illuminate\Http\Request;

final class BuilderRankApplicationController extends WebController
{
    use AuthorizesPermissions;

    public function show(
        Request $request,
        BuilderRankApplication $application,
    ) {
        if (
            $request->user()->id !== $application->account_id &&
            ! $this->can(WebReviewPermission::BUILD_RANK_APPS_VIEW)
        ) {
            abort(403);
        }

        return view('front.pages.builder-rank.builder-rank-status')
            ->with(compact('application'));
    }

    public function create(
        Request $request,
    ) {
        $minecraftUsername = $request->user()
            ?->minecraftAccount
            ?->first()
            ?->alias;

        $applicationInProgress = BuilderRankApplication::where('status', ApplicationStatus::PENDING->value)
            ->where('account_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($applicationInProgress !== null) {
            return to_route('front.rank-up.status', $applicationInProgress->id);
        }

        return view('front.pages.builder-rank.builder-rank-form')
            ->with(compact('minecraftUsername'));
    }

    public function store(
        BuilderRankApplicationRequest $request,
        CreateBuildRankApplication $createBuildRankApplication,
    ) {
        $validated = $request->validated();
        $account = $request->user();

        try {
            $application = $createBuildRankApplication->execute(
                account: $account,
                minecraftAlias: $validated['minecraft_username'],
                currentBuilderRank: BuilderRank::from($validated['current_builder_rank']),
                buildLocation: $validated['build_location'],
                buildDescription: $validated['build_description'],
                additionalNotes: $validated['additional_notes'],
            );
            return to_route('front.rank-up.status', $application->id);

        } catch (ApplicationAlreadyInProgressException) {
            return redirect()
                ->back()
                ->with(['error' => 'You cannot submit another application while you have another application under review']);
        }
    }
}
