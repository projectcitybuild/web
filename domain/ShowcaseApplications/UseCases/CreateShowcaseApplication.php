<?php

namespace Domain\ShowcaseApplications\UseCases;

use Carbon\Carbon;
use Domain\ShowcaseApplications\Entities\ApplicationStatus;
use Domain\ShowcaseApplications\Exceptions\ApplicationAlreadyInProgressException;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\ShowcaseApplication;
use Entities\Notifications\ShowcaseApplicationSubmittedNotification;
use Repositories\ShowcaseApplications\ShowcaseApplicationRepository;

class CreateShowcaseApplication
{
    public function __construct(
        private readonly ShowcaseApplicationRepository $applicationRepository,
    ) {
    }

    public function execute(
        Account $account,
        string $title,
        string $warpName,
        string $description,
        string $creators,
        int $x,
        int $y,
        int $z,
        float $pitch,
        float $yaw,
        string $world,
        Carbon $builtAt,
    ): ShowcaseApplication {
        $existingApplication = $this->applicationRepository->firstActive(
            accountId: $account->getKey(),
        );
        if ($existingApplication !== null) {
            throw new ApplicationAlreadyInProgressException();
        }

        $application = $this->applicationRepository->create(
            accountId: $account->getKey(),
            title: $title,
            warpName: $warpName,
            description: $description,
            creators: $creators,
            x: $x,
            y: $y,
            z: $z,
            pitch: $pitch,
            yaw: $yaw,
            world: $world,
            builtAt: $builtAt,
            status: ApplicationStatus::PENDING,
        );

        $application->notify(
            new ShowcaseApplicationSubmittedNotification($application)
        );

        return $application;
    }
}
