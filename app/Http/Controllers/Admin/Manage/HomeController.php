<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Http\Data\ManageHomeRecentActivityData;
use App\Models\Account;
use App\Models\Audit;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Inertia\Response;

class HomeController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function __invoke(Request $request): Response
    {
        $this->requires('web.manage.*');

        $recentActivities = Audit::query()
            ->with([
                'auditable',
                'user' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                    Account::class => ['minecraftAccount'],
                ]),
            ])
            ->latest()
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(function (Audit $audit): array {
                $user = $audit->user;
                $subjectType = $audit->auditable
                    ? class_basename($audit->auditable)
                    : ucfirst(class_basename($audit->auditable_type ?? 'Unknown item'));

                return (new ManageHomeRecentActivityData(
                    id: 'audit-'.$audit->id,
                    actor: $user?->getAttribute('username')
                        ?? $user?->getAttribute('name')
                        ?? 'System',
                    avatarUuid: $user instanceof Account
                        ? $user->minecraftAccount->first()?->uuid
                        : null,
                    action: $audit->human_action,
                    event: $audit->event,
                    subject: $subjectType.($audit->auditable_id !== null ? ' #'.$audit->auditable_id : ''),
                    createdAt: $audit->created_at?->toIso8601String() ?? '',
                    when: $audit->created_at?->shortRelativeDiffForHumans() ?? '',
                ))->toArray();
            })
            ->values()
            ->all();

        $props = ['recentActivities' => $recentActivities];

        if ($request->user()->isAdmin()) {
            $props['activityUrl'] = route('auditing.index');
        }

        return $this->inertiaRender('Home', $props);
    }
}
