<?php

namespace Domain\BuilderRankApplications\UseCases;

use Domain\BuilderRankApplications\Entities\ApplicationStatus;
use Domain\BuilderRankApplications\Entities\BuilderRank;
use Domain\BuilderRankApplications\Exceptions\ApplicationAlreadyInProgressException;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BuilderRankApplication;
use Entities\Notifications\BuilderRankAppSubmittedNotification;
use Entities\Repositories\BuilderRankApplicationRepository;
use Illuminate\Support\Facades\Http;

class CreateBuildRankApplicationUseCase
{
    public function __construct(
        private BuilderRankApplicationRepository $applicationRepository,
    ) {}

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

        return $application;
    }
}
