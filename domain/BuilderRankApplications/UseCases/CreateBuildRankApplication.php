<?php

namespace Domain\BuilderRankApplications\UseCases;

use App\Models\Account;
use App\Models\BuilderRankApplication;
use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Domain\BuilderRankApplications\Entities\BuilderRank;
use Domain\BuilderRankApplications\Exceptions\ApplicationAlreadyInProgressException;
use Entities\Notifications\BuilderRankAppSubmittedNotification;
use Repositories\BuilderRankApplicationRepository;

class CreateBuildRankApplication
{
    public function __construct(
        private readonly BuilderRankApplicationRepository $applicationRepository,
    ) {
    }

    /**
     * @throws ApplicationAlreadyInProgressException if the account already has an application in progress
     */
    public function execute(
        Account $account,
        string $minecraftAlias,
        BuilderRank $currentBuilderRank,
        string $buildLocation,
        string $buildDescription,
        ?string $additionalNotes,
    ): BuilderRankApplication {
        $existingApplication = $this->applicationRepository->countActive(
            accountId: $account->getKey(),
        );

        if ($existingApplication > 0) {
            throw new ApplicationAlreadyInProgressException();
        }

        $application = $this->applicationRepository->create(
            accountId: $account->getKey(),
            minecraftAlias: $minecraftAlias,
            currentBuilderRank: $currentBuilderRank,
            buildLocation: $buildLocation,
            buildDescription: $buildDescription,
            additionalNotes: $additionalNotes,
            status: ApplicationStatus::IN_PROGRESS,
        );

        $application->notify(new BuilderRankAppSubmittedNotification($application));

        return $application;
    }
}
