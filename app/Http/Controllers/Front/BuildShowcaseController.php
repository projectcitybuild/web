<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use Carbon\Carbon;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\ShowcaseApplication;
use Entities\Notifications\ShowcaseApplicationSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

final class BuildShowcaseController extends WebController
{
    public function index(Request $request)
    {
        $minecraftUsername = $request->user()
            ?->minecraftAccount?->first()
            ?->aliases?->first()
            ?->alias;

        $applicationInProgress = null;
        if ($request->user() !== null) {
            $applicationInProgress = ShowcaseApplication::where('account_id', $request->user()->getKey())->first();
        }

        return view('front.pages.build-showcase.form')
            ->with(compact('minecraftUsername', 'applicationInProgress'));
    }

    public function store(Request $request)
    {
        /** @var Account $account */
        $account = $request->user();

        if ($account === null) {
            return redirect()
                ->back()
                ->withErrors('You must be logged-in to submit a build to the showcase');
        }

        $applicationInProgress = ShowcaseApplication::where('account_id', $request->user()->getKey())->first();
        if ($applicationInProgress !== null) {
            return redirect()
                ->back()
                ->withErrors('You cannot submit another application while you have another application under review');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|alpha_dash',
            'title' => 'string',
            'description' => 'string',
            'creators' => 'string',
            'location_world' => 'required|string',
            'location_x' => 'required|integer',
            'location_y' => 'required|integer',
            'location_z' => 'required|integer',
            'location_pitch' => 'required|numeric',
            'location_yaw' => 'required|numeric',
            'built_at' => 'date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $application = ShowcaseApplication::create(
            array_merge($request->all(), [
                'account_id' => $account->getKey(),
                'status' => ApplicationStatus::IN_PROGRESS,
                'built_at' => Carbon::createFromTimeString($request->get('built_at')),
            ])
        );

        $application->notify(new ShowcaseApplicationSubmittedNotification($application));

        return view('front.pages.build-showcase.form-success')
            ->with(compact('application'));
    }

    public function show(
        Request $request,
        int $applicationId,
    ) {
        $application = ShowcaseApplication::find($applicationId);
        if ($application === null) {
            abort(404);
        }
        if ($request->user()->getKey() !== $application->account_id) {
            abort(403);
        }

        return view('front.pages.build-showcase.status')
            ->with(compact('application'));
    }
}
