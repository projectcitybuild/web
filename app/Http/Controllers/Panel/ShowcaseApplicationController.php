<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use App\Http\Requests\ShowcaseApplicationUpdateRequest;
use Domain\Panel\Exceptions\NoPlayerForActionException;
use Domain\ShowcaseApplications\Entities\ApplicationStatus;
use Domain\ShowcaseApplications\Exceptions\ApplicationAlreadyDecidedException;
use Domain\ShowcaseApplications\UseCases\UpdateShowcaseApplication;
use Entities\Models\Eloquent\ShowcaseApplication;
use Entities\Notifications\ShowcaseApplicationUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Repositories\ShowcaseApplications\ShowcaseApplicationRepository;

class ShowcaseApplicationController extends WebController
{
    public function index(
        Request $request,
        ShowcaseApplicationRepository $applicationRepository,
    ) {
        $applications = $applicationRepository->allWithPriority(perPage: 100);

        return view('admin.showcase-applications.index')
            ->with(compact('applications'));
    }

    public function show(
        Request $request,
        int $applicationId,
        ShowcaseApplicationRepository $applicationRepository,
    ) {
        $application = $applicationRepository->find(applicationId: $applicationId);

        return view('admin.showcase-applications.show')
            ->with(compact('application'));
    }

    public function update(
        UpdateShowcaseApplication $useCase,
        ShowcaseApplicationUpdateRequest $request,
        ShowcaseApplication $application,
    ) {
        try {
            $useCase->execute(
                application: $application,
                decidingAccount: $request->user(),
                decisionNote: $request->get('decision_note'),
                newStatus: ApplicationStatus::from($request->get('status'))
            );
        } catch (ApplicationAlreadyDecidedException $e) {
            throw ValidationException::withMessages([
                'error' => ['This appeal has already been decided.'],
            ]);
        } catch (NoPlayerForActionException $e) {
            throw ValidationException::withMessages([
                'error' => ['Please link a Minecraft account before deciding ban appeals.'],
            ]);
        }

        $application->notify(
            new ShowcaseApplicationUpdatedNotification(
                applicationLink: route('front.appeal.show', $application),
            )
        );

        return redirect()->route('front.panel.showcase-apps.show', $application);
    }
}
