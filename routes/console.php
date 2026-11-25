<?php

use App\Domains\Bans\Data\UnbanType;
use App\Domains\BuilderRankApplications\Services\BuilderRankReminderService;
use App\Domains\Donations\UseCases\ExpireDonorPerks;
use App\Domains\HealthCheck\Data\SchedulerHealthCheck;
use App\Domains\HealthCheck\HealthCheckReporter;
use App\Domains\Instagram\InstagramMediaFeed;
use App\Domains\Instagram\Jobs\RefreshInstagramAccessTokenJob;
use App\Domains\Instagram\Jobs\RefreshInstagramMediaFeedJob;
use App\Domains\Permissions\WebManagePermission;
use App\Models\GamePlayerBan;
use App\Models\Permission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\SitemapGenerator;

Artisan::command('sitemap:generate', function () {
    try {
        SitemapGenerator::create(config('app.url'))
            ->setMaximumCrawlCount(50_000)
            ->shouldCrawl(function (string $url): bool {
                $query = parse_url($url, PHP_URL_QUERY);

                return ! is_string($query) || ! str_contains($query, 'page=');
            })
            ->writeToFile(public_path('sitemap.xml'));

        Log::info('Generated sitemap.xml');
    } catch (Exception $e) {
        Log::error('Failed to generate sitemap.xml', ['exception' => $e]);
    }
});

Artisan::command('donor-perks:expire', function () {
    app()->make(ExpireDonorPerks::class)->execute();
});

Artisan::command('bans:expire', function () {
    GamePlayerBan::whereNull('unbanned_at')
        ->whereDate('expires_at', '<=', now())
        ->update([
            'unbanned_at' => now(),
            'unban_type' => UnbanType::EXPIRED->value,
        ]);
});

Artisan::command('healthcheck:scheduler', function () {
    $reporter = new HealthCheckReporter(
        healthCheck: new SchedulerHealthCheck,
    );
    $reporter->success();
});

Artisan::command('build-rank-apps:remind', function () {
    (new BuilderRankReminderService)->remind();
});

Artisan::command('permissions:seed', function () {
    foreach (WebManagePermission::cases() as $permission) {
        $exists = Permission::where('name', $permission->value)->exists();
        if ($exists) {
            $this->info('Skipping '.$permission->value.': already exists.');
        } else {
            Permission::create(['name' => $permission->value]);
            $this->info('Created '.$permission->value);
        }
    }
});

Artisan::command('instagram:cache:clear', function () {
    app(InstagramMediaFeed::class)->clearCache();

    $this->info('Instagram media feed cache and images cleared.');
})->purpose('Clear cached Instagram media and images');

Artisan::command('instagram:feed:refresh', function () {
    dispatch(new RefreshInstagramMediaFeedJob);

    $this->info('Instagram media feed refresh job queued.');
})->purpose('Queue an Instagram media feed refresh');

Artisan::command('instagram:token:refresh', function () {
    dispatch(new RefreshInstagramAccessTokenJob);

    $this->info('Instagram access token refresh job queued.');
})->purpose('Queue an Instagram access token refresh');
