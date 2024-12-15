<?php

namespace App\Http\Controllers\Front;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Data\BuilderRank;
use App\Domains\BuilderRankApplications\Exceptions\ApplicationAlreadyInProgressException;
use App\Domains\BuilderRankApplications\UseCases\CreateBuildRankApplication;
use App\Http\Controllers\WebController;
use App\Http\Requests\BuilderRankApplicationRequest;
use App\Models\BuilderRankApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

final class BuilderRankApplicationController extends WebController
{
    public function index(
        Request $request,
    ) {
        $minecraftUsername = $request->user()
            ?->minecraftAccount
            ?->first()
            ?->alias;

        $applicationInProgress = BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)
            ->where('account_id', $request->user()->getKey())
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($applicationInProgress !== null) {
            return redirect()
                ->route('front.rank-up.status', $applicationInProgress->getKey());
        }

        return view('front.pages.builder-rank.builder-rank-form')
            ->with(compact('minecraftUsername'));
    }

    public function show(
        Request $request,
        BuilderRankApplication $application,
    ) {
        Gate::authorize('view', $application);

        return view('front.pages.builder-rank.builder-rank-status')
            ->with(compact('application'));
    }

    public function store(
        BuilderRankApplicationRequest $request,
        CreateBuildRankApplication $createBuildRankApplication,
    ) {
        Gate::authorize('create', BuilderRankApplication::class);

        $input = $request->validated();
        $account = $request->user();

        try {
            $application = $createBuildRankApplication->execute(
                account:  $account,
                minecraftAlias:  $input['minecraft_username'],
                currentBuilderRank: BuilderRank::from($input['current_builder_rank']),
                buildLocation: $input['build_location'],
                buildDescription: $input['build_description'],
                additionalNotes: $request->get('additional_notes'),
            );
            return redirect()
                ->route('front.rank-up.status', $application->getKey());

        } catch (ApplicationAlreadyInProgressException) {
            return redirect()
                ->back()
                ->with(['error' => 'You cannot submit another application while you have another application under review']);
        }
    }
}
