<?php

namespace App\Http\Controllers\Front;

use App\Domains\BuilderRankApplications\Data\BuilderRank;
use App\Domains\BuilderRankApplications\Exceptions\ApplicationAlreadyInProgressException;
use App\Domains\BuilderRankApplications\UseCases\CreateBuildRankApplication;
use App\Http\Controllers\WebController;
use App\Http\Requests\BuilderRankApplicationRequest;
use App\Models\Account;
use App\Models\BuilderRankApplication;
use Illuminate\Http\Request;
use Repositories\BuilderRankApplicationRepository;

final class BuilderRankApplicationController extends WebController
{
    public function index(
        Request $request,
        BuilderRankApplicationRepository $applicationRepository,
    ) {
        $minecraftUsername = $request->user()
            ?->minecraftAccount?->first()
            ?->aliases?->first()
            ?->alias;

        $applicationInProgress = $applicationRepository->firstActive(
            accountId: $request->user()->getKey(),
        );
        if ($applicationInProgress !== null) {
            return redirect()
                ->route('front.rank-up.status', $applicationInProgress->getKey());
        }

        return view('front.pages.builder-rank.builder-rank-form')
            ->with(compact('minecraftUsername'));
    }

    public function store(
        BuilderRankApplicationRequest $request,
        CreateBuildRankApplication $createBuildRankApplication,
    ) {
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

    public function show(
        Request $request,
        int $applicationId,
    ) {
        $application = BuilderRankApplication::find($applicationId);
        if ($application === null) {
            abort(404);
        }
        if ($request->user()->getKey() !== $application->account_id) {
            abort(403);
        }

        return view('front.pages.builder-rank.builder-rank-status')
            ->with(compact('application'));
    }
}
