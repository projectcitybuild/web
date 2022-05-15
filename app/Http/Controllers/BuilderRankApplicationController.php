<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuilderRankApplicationRequest;
use App\Http\WebController;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Domain\BuilderRankApplications\Entities\BuilderRank;
use Entities\Models\Eloquent\BuilderRankApplication;
use Illuminate\Http\Request;

final class BuilderRankApplicationController extends WebController
{
    public function index()
    {
        return view('v2.front.pages.builder-rank.builder-rank-form');
    }

    public function store(BuilderRankApplicationRequest $request)
    {
        $input = $request->validated();

        if ($request->user() === null) {
            return redirect()
                ->back()
                ->withErrors('You must be logged-in to submit a Builder Rank application');
        }

        $application = BuilderRankApplication::create([
            'account_id' => $request->user()->getKey(),
            'minecraft_alias' => $input['minecraft_username'],
            'current_builder_rank' => BuilderRank::from($input['current_builder_rank']),
            'build_location' => $input['build_location'],
            'build_description' => $input['build_description'],
            'additional_notes' => $request->get('additional_notes'),
            'status' => ApplicationStatus::IN_PROGRESS->value,
            'closed_at' => null,
        ]);

        return view('v2.front.pages.builder-rank.builder-rank-success')->with('application', $application);
    }

    public function show(Request $request, int $applicationId)
    {
        $application = BuilderRankApplication::find($applicationId);
        if ($application === null) {
            abort(404);
        }
        if ($request->user()->getKey() !== $application->account_id) {
            abort(403);
        }

        return view('v2.front.pages.builder-rank.builder-rank-status')->with('application', $application);
    }
}
