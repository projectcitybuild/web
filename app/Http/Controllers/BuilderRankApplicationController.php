<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuilderRankApplicationRequest;
use App\Http\WebController;
use Domain\BuilderRankApplications\ApplicationStatus;
use Domain\BuilderRankApplications\BuilderRank;
use Entities\Models\Eloquent\BuilderRankApplication;

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

        $rankApplication = BuilderRankApplication::create([
            'account_id' => $request->user()->getKey(),
            'minecraft_alias' => $input['minecraft_username'],
            'current_builder_rank' => BuilderRank::from($input['current_builder_rank']),
            'build_location' => $input['build_location'],
            'build_description' => $input['build_description'],
            'additional_notes' => $request->get('additional_notes'),
            'status' => ApplicationStatus::IN_PROGRESS->value,
            'closed_at' => null,
        ]);

        return view('v2.front.pages.builder-rank.builder-rank-success')->with('application', $rankApplication);
    }
}
