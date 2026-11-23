<?php

namespace App\Http\Controllers\Admin\Manage\Players;

use App\Http\Controllers\WebController;
use App\Http\Data\AdminAuditListEntryData;
use App\Models\Account;
use App\Models\Audit;
use App\Models\MinecraftPlayer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MinecraftPlayerAuditController extends WebController
{
    public function index(Request $request, MinecraftPlayer $player): LengthAwarePaginator
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $playerMorphType = $player->getMorphClass();
        $accountMorphType = (new Account)->getMorphClass();

        $audits = Audit::query()
            ->where(function (Builder $query) use ($accountMorphType, $player, $playerMorphType) {
                $query->where(function (Builder $query) use ($player, $playerMorphType) {
                    $query->where('auditable_type', $playerMorphType)
                        ->where('auditable_id', $player->id);
                })
                    ->orWhere(function (Builder $query) use ($player, $playerMorphType) {
                        $query->where('user_type', $playerMorphType)
                            ->where('user_id', $player->id);
                    });

                if ($player->account_id !== null) {
                    $query->orWhere(function (Builder $query) use ($accountMorphType, $player) {
                        $query->where('user_type', $accountMorphType)
                            ->where('user_id', $player->account_id);
                    });
                }
            })
            ->with(['auditable', 'user'])
            ->orderByDesc('created_at')
            ->paginate(50)
            ->through(function (Audit $audit) {
                $subjectType = $audit->auditable
                    ? class_basename($audit->auditable)
                    : ucfirst(class_basename($audit->auditable_type ?? 'Unknown item'));

                return (new AdminAuditListEntryData(
                    id: $audit->id,
                    actor: $audit->user instanceof MinecraftPlayer
                        ? ($audit->user->getActivitySubjectName() ?? 'System')
                        : ($audit->user?->username ?? 'System'),
                    action: $audit->human_action,
                    event: $audit->event,
                    subject: $subjectType.($audit->auditable_id !== null ? ' #'.$audit->auditable_id : ''),
                    createdAt: $audit->created_at->toIso8601String(),
                    showUrl: route('auditing.activity.audit.show', $audit),
                ))->toArray();
            });

        return $audits;
    }
}
