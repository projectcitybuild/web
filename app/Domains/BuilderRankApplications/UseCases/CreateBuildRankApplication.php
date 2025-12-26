<?php

namespace App\Domains\BuilderRankApplications\UseCases;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Data\BuilderRank;
use App\Domains\BuilderRankApplications\Exceptions\ApplicationAlreadyInProgressException;
use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppSubmittedNotification;
use App\Models\Account;
use App\Models\BuilderRankApplication;

class CreateBuildRankApplication
{
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
        $existingApplication = BuilderRankApplication::where('status', ApplicationStatus::PENDING->value)
            ->where('account_id', $account->getKey())
            ->count();

        if ($existingApplication > 0) {
            throw new ApplicationAlreadyInProgressException;
        }

        $application = BuilderRankApplication::create([
            'account_id' => $account->getKey(),
            'minecraft_alias' => $minecraftAlias,
            'current_builder_rank' => $currentBuilderRank->humanReadable(),
            'build_location' => $buildLocation,
            'build_description' => $buildDescription,
            'additional_notes' => $additionalNotes,
            'status' => ApplicationStatus::PENDING->value,
            'next_reminder_at' => now()->addWeek(),
            'closed_at' => null,
        ]);

        $application->notify(new BuilderRankAppSubmittedNotification($application));

        return $application;
    }
}
