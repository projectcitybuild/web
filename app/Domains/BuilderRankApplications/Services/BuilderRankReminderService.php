<?php

namespace App\Domains\BuilderRankApplications\Services;

use App\Domains\BuilderRankApplications\Notifications\BuilderRankAppReminderNotification;
use App\Models\BuilderRankApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

final class BuilderRankReminderService
{
    public function remind()
    {
        $openApps = BuilderRankApplication::where('closed_at', null)
            ->where('next_reminder_at', '<=', now())
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        if ($openApps->isEmpty()) {
            Log::debug('No open builder rank applications need a reminder');
            return;
        }

        $channel = config('discord.webhook_architect_channel');
        throw_if(empty($channel), 'No discord channel set for architects');

        DB::transaction(function () use ($openApps, $channel) {
            foreach ($openApps as $app) {
                $app->next_reminder_at = now()->addDays(3);
                $app->save();
            }
            Notification::route('discord', $channel)
                ->notify(new BuilderRankAppReminderNotification($openApps));
        });

        Log::info('Sent reminder for open builder rank applications', [
            'apps' => $openApps,
        ]);
    }
}
