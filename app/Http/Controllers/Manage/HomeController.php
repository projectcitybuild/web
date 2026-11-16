<?php

namespace App\Http\Controllers\Manage;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function __invoke(Request $request)
    {
        $this->requires('web.manage.*');

        $props = [
            'recentActivities' => Activity::query()
                ->with([
                    'causer' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                        Account::class => ['minecraftAccount'],
                    ]),
                ])
                ->latest()
                ->limit(8)
                ->get()
                ->map(function (Activity $activity): array {
                    $causer = $activity->causer;

                    return [
                        'id' => $activity->id,
                        'actor' => $causer?->getAttribute('username')
                            ?? $causer?->getAttribute('name')
                            ?? $activity->systemCauser?->displayName()
                            ?? 'System',
                        'avatarUuid' => $causer instanceof Account
                            ? $causer->minecraftAccount->first()?->uuid
                            : null,
                        'action' => $activity->human_action,
                        'when' => $activity->created_at?->shortRelativeDiffForHumans() ?? '',
                    ];
                })
                ->values()
                ->all(),
        ];

        if ($request->user()->isAdmin()) {
            $props['activityUrl'] = route('manage.activity.index');
        }

        return $this->inertiaRender('Home', $props);
    }
}
