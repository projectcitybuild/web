<?php

namespace App\Core\Domains\Scheduling;

use App\Domains\HealthCheck\Jobs\MailCanaryJob;
use App\Domains\HealthCheck\Jobs\QueueWorkerHeartbeatJob;
use App\Domains\Instagram\Jobs\RefreshInstagramAccessTokenJob;
use App\Domains\Instagram\Jobs\RefreshInstagramMediaFeedJob;
use App\Domains\PlayerMail\Jobs\RecoverPlayerMailBroadcastsJob;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

final class ScheduleRegistry
{
    public function register(Schedule $schedule): void
    {
        $withAdminTitle = static function (Event $event, string $title): Event {
            $event->withAttributes([
                'admin_title' => $title,
                'admin_subtitle' => $event->getSummaryForDisplay(),
            ]);

            return $event;
        };

        $withAdminTitle($schedule->command('model:prune'), 'Prune expired models')
            ->everyFifteenMinutes();

        $withAdminTitle($schedule->command('backup:clean'), 'Clean old backups')
            ->withoutOverlapping()
            ->daily();

        $withAdminTitle($schedule->command('backup:run'), 'Create application backup')
            ->withoutOverlapping()
            ->daily();

        $withAdminTitle($schedule->command('backup:monitor'), 'Monitor backup health')
            ->withoutOverlapping()
            ->daily();

        $withAdminTitle($schedule->command('sitemap:generate'), 'Generate sitemap')
            ->daily();

        $withAdminTitle($schedule->command('donor-perks:expire'), 'Expire donor perks')
            ->everyFiveMinutes();

        $withAdminTitle($schedule->command('bans:expire'), 'Expire player bans')
            ->everyFifteenMinutes();

        $withAdminTitle($schedule->command('healthcheck:scheduler'), 'Report scheduler health')
            ->runInBackground()
            ->evenInMaintenanceMode()
            ->everyFifteenMinutes();

        $withAdminTitle($schedule->job(new QueueWorkerHeartbeatJob), 'Check queue worker health')
            ->everyFiveMinutes();

        $withAdminTitle($schedule->job(new MailCanaryJob), 'Send mail canary')
            ->dailyAt('09:00')
            ->timezone('UTC')
            ->onOneServer();

        $withAdminTitle(
            $schedule->job(new RefreshInstagramMediaFeedJob)->name('instagram.media-feed.refresh'),
            'Refresh Instagram media feed',
        )
            ->everyThirtyMinutes()
            ->withoutOverlapping(10)
            ->onOneServer();

        $withAdminTitle(
            $schedule->job(new RefreshInstagramAccessTokenJob)->name('instagram.access-token.refresh'),
            'Refresh Instagram access token',
        )
            ->weekly()
            ->timezone('UTC')
            ->withoutOverlapping(10)
            ->onOneServer();

        $withAdminTitle($schedule->command('build-rank-apps:remind'), 'Remind architects of open applications')
            ->runInBackground()
            ->evenInMaintenanceMode()
            ->hourly();

        $withAdminTitle(
            $schedule->job(new RecoverPlayerMailBroadcastsJob)->name('player-mail.broadcast-recovery'),
            'Recover player mail broadcasts',
        )
            ->everyMinute()
            ->withoutOverlapping(10);
    }
}
