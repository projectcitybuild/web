<?php

namespace Repositories;

use Domain\ShowcaseApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\ShowcaseApplication;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @final
 */
class ShowcaseApplicationRepository
{
//    public function create(
//      int $accountId,
//      string $minecraftAlias,
//      BuilderRank $currentBuilderRank,
//      string $buildLocation,
//      string $buildDescription,
//      ?string $additionalNotes,
//      ApplicationStatus $status,
//    ): BuilderRankApplication {
//        return BuilderRankApplication::create([
//            'account_id' => $accountId,
//            'minecraft_alias' => $minecraftAlias,
//            'current_builder_rank' => $currentBuilderRank->humanReadable(),
//            'build_location' => $buildLocation,
//            'build_description' => $buildDescription,
//            'additional_notes' => $additionalNotes,
//            'status' => $status->value,
//            'closed_at' => null,
//        ]);
//    }

//    public function countActive(int $accountId): int
//    {
//        return BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)
//            ->where('account_id', $accountId)
//            ->count();
//    }
//
//    public function firstActive(int $accountId): ?BuilderRankApplication
//    {
//        return BuilderRankApplication::where('status', ApplicationStatus::IN_PROGRESS->value)
//            ->where('account_id', $accountId)
//            ->orderBy('created_at', 'DESC')
//            ->first();
//    }
//
    public function first(int $applicationId): ?ShowcaseApplication
    {
        return ShowcaseApplication::find($applicationId);
    }

    public function allWithPriority(int $perPage): LengthAwarePaginator
    {
        return ShowcaseApplication::orderbyRaw('FIELD(status, '.ApplicationStatus::PENDING->value.') DESC')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updateDecision(
        ShowcaseApplication $application,
        string $decisionNote,
        int $deciderPlayerMinecraftId,
        ApplicationStatus $newStatus,
    ): ShowcaseApplication {
        $application->decision_note = $decisionNote;
        $application->decider_player_minecraft_id = $deciderPlayerMinecraftId;
        $application->status = $newStatus;
        $application->decided_at = now();
        $application->save();

        activity()
            ->on($application)
            ->log(strtolower($newStatus->humanReadable()));

        return $application;
    }
}
