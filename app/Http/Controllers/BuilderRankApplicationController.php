<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuilderRankApplicationRequest;
use App\Http\WebController;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Domain\BuilderRankApplications\Entities\BuilderRank;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Notifications\BuilderRankAppSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

final class BuilderRankApplicationController extends WebController
{
    public function index(Request $request)
    {
        $minecraftUsername = $request->user()
            ->minecraftAccount->first()
            ?->aliases?->first()
            ?->alias;

        $applicationInProgress = BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)
            ->where('account_id', $request->user()->getKey())
            ->orderBy('created_at', 'DESC')
            ->first();

        return view('v2.front.pages.builder-rank.builder-rank-form')
            ->with(compact('minecraftUsername', 'applicationInProgress'));
    }

    public function store(BuilderRankApplicationRequest $request)
    {
        $input = $request->validated();

        /** @var Account $account */
        $account = $request->user();

        if ($account === null) {
            return redirect()
                ->back()
                ->withErrors('You must be logged-in to submit a Builder Rank application');
        }

        $existingApplication = BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)
            ->where('account_id', $account->getKey())
            ->count();

        if ($existingApplication > 0) {
            return redirect()
                ->back()
                ->withErrors('You cannot submit another application while you have another application under review');
        }

        $application = BuilderRankApplication::create([
            'account_id' => $account->getKey(),
            'minecraft_alias' => $input['minecraft_username'],
            'current_builder_rank' => BuilderRank::from($input['current_builder_rank'])->humanReadable(),
            'build_location' => $input['build_location'],
            'build_description' => $input['build_description'],
            'additional_notes' => $request->get('additional_notes'),
            'status' => ApplicationStatus::IN_PROGRESS->value,
            'closed_at' => null,
        ]);

        $account->notify(new BuilderRankAppSubmittedNotification($application));

        // TODO: do this properly later...
        $webhook = config('discord.webhook_architect_channel');
        if (! empty($webhook)) {
            Http::post($webhook, [
                'content' => "A build rank application has arrived",
                'embeds' => [
                    [
                        "title" => "Build Rank Application",
                        "url" => route('front.panel.builder-ranks.show', $application->getKey()),
                        "color" => "7506394",
                        "fields" => [
                            [
                                "name" => "Current build rank",
                                "value" => $application->current_builder_rank,
                            ],
                            [
                                "name" => "Build location",
                                "value" => $application->build_location,
                            ],
                            [
                                "name" => "Build description",
                                "value" => $application->build_description,
                            ],
                            [
                                "name" => "Additional notes",
                                "value" => $application->additional_notes ?? "-",
                            ],
                        ],
                        "author" => [
                            "name" => $application->minecraft_alias,
                        ]
                    ]
                ],
            ]);
        }

        return view('v2.front.pages.builder-rank.builder-rank-success')
            ->with(compact('application'));
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

        return view('v2.front.pages.builder-rank.builder-rank-status')
            ->with(compact('application'));
    }
}