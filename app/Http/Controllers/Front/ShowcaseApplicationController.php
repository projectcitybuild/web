<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use Carbon\Carbon;
use Domain\ShowcaseApplications\UseCases\CreateShowcaseApplication;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\ShowcaseApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Repositories\ShowcaseApplications\ShowcaseApplicationRepository;

final class ShowcaseApplicationController extends WebController
{
    public function index(
        Request $request,
        ShowcaseApplicationRepository $applicationRepository,
    ) {
        $minecraftUsername = $request->user()
            ?->minecraftAccount?->first()
            ?->aliases?->first()
            ?->alias;

        $applicationInProgress = null;
        if ($request->user() !== null) {
            $applicationInProgress = $applicationRepository->firstActive(accountId: $request->user()->getKey());
        }

        return view('front.pages.build-showcase.form')
            ->with(compact('minecraftUsername', 'applicationInProgress'));
    }

    public function store(
        Request $request,
        CreateShowcaseApplication $createShowcaseApplication,
    ) {
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

        $application = $createShowcaseApplication->execute(
            account: $account,
            title: $request->get('title'),
            warpName: $request->get('name'),
            description: $request->get('description'),
            creators: $request->get('creators'),
            x: $request->get('location_x'),
            y: $request->get('location_y'),
            z: $request->get('location_z'),
            pitch: $request->get('location_pitch'),
            yaw: $request->get('location_yaw'),
            world: $request->get('location_world'),
            builtAt: Carbon::createFromTimeString($request->get('built_at')),
        );

        return view('front.pages.build-showcase.form-success')
            ->with(compact('application'));
    }

    public function show(
        Request $request,
        int $applicationId,
        ShowcaseApplicationRepository $applicationRepository,
    ) {
        $application = $applicationRepository->find($applicationId);
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
