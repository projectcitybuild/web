<?php

namespace Repositories\ShowcaseApplications;

use Carbon\Carbon;
use Domain\ShowcaseApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\ShowcaseApplication;
use Illuminate\Pagination\LengthAwarePaginator;

final class ShowcaseApplicationEloquentRepository implements ShowcaseApplicationRepository
{
    public function create(
      int $accountId,
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
      ApplicationStatus $status,
    ): ShowcaseApplication {
        return ShowcaseApplication::create([
            'account_id' => $accountId,
            'title' => $title,
            'name' => $warpName,
            'description' => $description,
            'creators' => $creators,
            'location_x' => $x,
            'location_y' => $y,
            'location_z' => $z,
            'location_pitch' => $pitch,
            'location_yaw' => $yaw,
            'location_world' => $world,
            'built_at' => $builtAt,
            'status' => $status->value,
        ]);
    }

    public function firstActive(int $accountId): ?ShowcaseApplication
    {
        return ShowcaseApplication::where('status', ApplicationStatus::PENDING->value)
            ->where('account_id', $accountId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function find(int $applicationId): ?ShowcaseApplication
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
