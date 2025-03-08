<?php

use App\Core\Domains\Auditing\Causers\SystemCauser;
use App\Core\Domains\Auditing\Causers\SystemCauseResolver;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\Donations\UseCases\ExpireDonorPerks;
use App\Domains\HealthCheck\Data\SchedulerHealthCheck;
use App\Domains\HealthCheck\HealthCheckReporter;
use App\Models\GamePlayerBan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Spatie\Sitemap\SitemapGenerator;

Schedule::command('model:prune')
    ->everyFifteenMinutes();

Schedule::command('passport:purge')
    ->hourly();

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
    SitemapGenerator::create(config('app.url'))
        ->writeToFile(public_path('sitemap.xml'));
})->daily();

Artisan::command('donor-perks:expire', function () {
    app()->make(ExpireDonorPerks::class)->execute();
})->everyFiveMinutes();

Artisan::command('bans:prune', function () {
    GamePlayerBan::whereNull('unbanned_at')
        ->whereDate('expires_at', '<=', now())
        ->update([
            'unbanned_at' => now(),
            'unban_type' => UnbanType::EXPIRED->value,
        ]);
})->everyFifteenMinutes();

Artisan::command('healthcheck:scheduler', function () {
    $reporter = new HealthCheckReporter(
        healthCheck: new SchedulerHealthCheck(),
    );
    $reporter->success();
})->runInBackground()
  ->evenInMaintenanceMode()
  ->everyFifteenMinutes();
