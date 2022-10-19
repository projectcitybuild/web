<?php

namespace Domain\ShowcaseApplications\UseCases;

use Domain\Panel\Exceptions\NoPlayerForActionException;
use Domain\ShowcaseApplications\Entities\ApplicationStatus;
use Domain\ShowcaseApplications\Exceptions\ApplicationAlreadyDecidedException;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\ShowcaseApplication;
use Repositories\ShowcaseApplications\ShowcaseApplicationRepository;

class UpdateShowcaseApplication
{
    public function __construct(
        private readonly ShowcaseApplicationRepository $applicationRepository,
    ) {
    }

    public function execute(
        ShowcaseApplication $application,
        Account $decidingAccount,
        string $decisionNote,
        ApplicationStatus $newStatus,
    ) {
        if ($application->status->isDecided()) {
            throw new ApplicationAlreadyDecidedException();
        }
        if ($decidingAccount->minecraftAccount()->doesntExist()) {
            throw new NoPlayerForActionException();
        }

        $decidingPlayer = $decidingAccount->minecraftAccount->first();

        $this->applicationRepository->updateDecision(
            application: $application,
            decisionNote: $decisionNote,
            deciderPlayerMinecraftId: $decidingPlayer->getKey(),
            newStatus: $newStatus,
        );
    }
}
