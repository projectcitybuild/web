<?php

namespace Repositories;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\BuilderRankApplications\Data\BuilderRank;
use App\Models\BuilderRankApplication;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @deprecated
 */
class BuilderRankApplicationRepository
{
    public function create(
      int $accountId,
      string $minecraftAlias,
      BuilderRank $currentBuilderRank,
      string $buildLocation,
      string $buildDescription,
      ?string $additionalNotes,
      ApplicationStatus $status,
    ): BuilderRankApplication {
        return BuilderRankApplication::create([
            'account_id' => $accountId,
            'minecraft_alias' => $minecraftAlias,
            'current_builder_rank' => $currentBuilderRank->humanReadable(),
            'build_location' => $buildLocation,
            'build_description' => $buildDescription,
            'additional_notes' => $additionalNotes,
            'status' => $status->value,
            'closed_at' => null,
        ]);
    }

    public function countActive(int $accountId): int
    {
        return BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)
            ->where('account_id', $accountId)
            ->count();
    }

    public function firstActive(int $accountId): ?BuilderRankApplication
    {
        return BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)
            ->where('account_id', $accountId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function first(int $applicationId): ?BuilderRankApplication
    {
        return BuilderRankApplication::find($applicationId);
    }

    public function allWithPriority(int $perPage): LengthAwarePaginator
    {
        return BuilderRankApplication::orderbyRaw('FIELD(status, '.ApplicationStatus::IN_PROGRESS->value.') DESC')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function approve(BuilderRankApplication $application)
    {
        $application->status = ApplicationStatus::APPROVED->value;
        $application->closed_at = now();
        $application->save();
    }

    public function deny(BuilderRankApplication $application, string $reason)
    {
        $application->status = ApplicationStatus::DENIED->value;
        $application->denied_reason = $reason;
        $application->closed_at = now();
        $application->save();
    }
}
