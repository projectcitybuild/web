<?php

namespace Repositories\ShowcaseApplications;

use Domain\ShowcaseApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\ShowcaseApplication;
use Illuminate\Pagination\LengthAwarePaginator;

interface ShowcaseApplicationRepository
{
    public function firstActive(int $accountId): ?ShowcaseApplication;

    public function find(int $applicationId): ?ShowcaseApplication;

    public function allWithPriority(int $perPage): LengthAwarePaginator;

    public function updateDecision(
        ShowcaseApplication $application,
        string $decisionNote,
        int $deciderPlayerMinecraftId,
        ApplicationStatus $newStatus,
    ): ShowcaseApplication;
}
