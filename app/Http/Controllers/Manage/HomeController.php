<?php

namespace App\Http\Controllers\Manage;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Audit;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function __invoke(Request $request)
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

                return [
                    'id' => 'audit-'.$audit->id,
                    'actor' => $user?->getAttribute('username')
                        ?? $user?->getAttribute('name')
                        ?? 'System',
                    'avatarUuid' => $user instanceof Account
                        ? $user->minecraftAccount->first()?->uuid
                        : null,
                    'action' => $audit->human_action,
                    'when' => $audit->created_at?->shortRelativeDiffForHumans() ?? '',
                ];
            })
            ->values()
            ->all();

        $props = ['recentActivities' => $recentActivities];

        if ($request->user()->isAdmin()) {
            $props['activityUrl'] = route('manage.activity.index');
        }

        return $this->inertiaRender('Home', $props);
    }
}
