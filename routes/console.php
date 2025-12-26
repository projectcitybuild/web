<?php

use App\Domains\Bans\Data\UnbanType;
use App\Domains\BuilderRankApplications\Services\BuilderRankReminderService;
use App\Domains\Donations\UseCases\ExpireDonorPerks;
use App\Domains\HealthCheck\Data\SchedulerHealthCheck;
use App\Domains\HealthCheck\HealthCheckReporter;
use App\Models\GamePlayerBan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Psr\Http\Message\UriInterface;
use Spatie\Sitemap\SitemapGenerator;

Schedule::command('model:prune')
    ->everyFifteenMinutes();

Schedule::command('backup:clean')
    ->withoutOverlapping()
    ->daily();

Schedule::command('backup:run')
    ->withoutOverlapping()
    ->daily();

Schedule::command('backup:monitor')
    ->withoutOverlapping()
    ->daily();

Artisan::command('sitemap:generate', function () {
    try {
        SitemapGenerator::create(config('app.url'))
            ->setMaximumCrawlCount(50_000)
            ->shouldCrawl(function (UriInterface $uri) {
                if (str_contains($uri->getQuery(), 'page=')) {
                    return false;
                }
                return true;
            })
            ->writeToFile(public_path('sitemap.xml'));

        Log::info('Generated sitemap.xml');
    } catch (Exception $e) {
        Log::error('Failed to generate sitemap.xml', $e);
    }
})->daily();

Artisan::command('donor-perks:expire', function () {
    app()->make(ExpireDonorPerks::class)->execute();
})->everyFiveMinutes();

Artisan::command('bans:expire', function () {
    GamePlayerBan::whereNull('unbanned_at')
        ->whereDate('expires_at', '<=', now())
        ->update([
            'unbanned_at' => now(),
            'unban_type' => UnbanType::EXPIRED->value,
        ]);
})->everyFifteenMinutes();

Artisan::command('healthcheck:scheduler', function () {
    $reporter = new HealthCheckReporter(
        healthCheck: new SchedulerHealthCheck,
    );
    $reporter->success();
})->runInBackground()
    ->evenInMaintenanceMode()
    ->everyFifteenMinutes();

Artisan::command('build-rank-apps:remind', function () {
    (new BuilderRankReminderService)->remind();
})->runInBackground()
    ->evenInMaintenanceMode()
    ->daily();
