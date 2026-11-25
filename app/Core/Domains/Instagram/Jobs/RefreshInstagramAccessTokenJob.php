<?php

namespace App\Core\Domains\Instagram\Jobs;

use App\Core\Domains\Instagram\InstagramMediaFeed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class RefreshInstagramAccessTokenJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 300];
    public int $timeout = 30;

    public function handle(InstagramMediaFeed $instagramMediaFeed): void
    {
        $instagramMediaFeed->refreshAccessToken();
    }
}
