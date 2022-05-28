<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuilderRankApplicationRequest;
use App\Http\WebController;
use Domain\BuilderRankApplications\Entities\BuilderRank;
use Domain\BuilderRankApplications\Exceptions\ApplicationAlreadyInProgressException;
use Domain\BuilderRankApplications\UseCases\CreateBuildRankApplicationUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Repositories\BuilderRankApplicationRepository;
use Illuminate\Http\Request;

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

        $applicationInProgress = null;
        if ($request->user() !== null) {
            $applicationInProgress = $applicationRepository->firstActive(
                accountId: $request->user()->getKey(),
            );
        }

        return view('v2.front.pages.builder-rank.builder-rank-form')
            ->with(compact('minecraftUsername', 'applicationInProgress'));
    }

    public function store(
        BuilderRankApplicationRequest $request,
        CreateBuildRankApplicationUseCase $createBuildRankApplication,
    ) {
        $input = $request->validated();

        /** @var Account $account */
        $account = $request->user();

        if ($account === null) {
            return redirect()
                ->back()
                ->withErrors('You must be logged-in to submit a Builder Rank application');
        }
        try {
            $application = $createBuildRankApplication->execute(
                account:  $account,
                minecraftAlias:  $input['minecraft_username'],
                currentBuilderRank: BuilderRank::from($input['current_builder_rank']),
                buildLocation: $input['build_location'],
                buildDescription: $input['build_description'],
                additionalNotes: $request->get('additional_notes'),
            );
        } catch (ApplicationAlreadyInProgressException) {
            return redirect()
                ->back()
                ->withErrors('You cannot submit another application while you have another application under review');
        }

        return view('v2.front.pages.builder-rank.builder-rank-success')
            ->with(compact('application'));
    }

    public function show(
        Request $request,
        int $applicationId,
        BuilderRankApplicationRepository $applicationRepository,
    ) {
        $application = $applicationRepository->first(applicationId: $applicationId);
        if ($application === null) {
            abort(404);
        }
        if ($request->user()->getKey() !== $application->account_id) {
            abort(403);
        }

        return view('v2.front.pages.builder-rank.builder-rank-status')
            ->with(compact('application'));
    }
}
