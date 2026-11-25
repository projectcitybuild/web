<?php

use App\Domains\Instagram\Jobs\RefreshInstagramAccessTokenJob;
use App\Domains\Instagram\Jobs\RefreshInstagramMediaFeedJob;
use Illuminate\Support\Facades\Queue;

it('queues an Instagram media feed refresh job', function () {
    Queue::fake([RefreshInstagramMediaFeedJob::class]);

    $this->artisan('instagram:media-feed:refresh')
        ->expectsOutput('Instagram media feed refresh job queued.')
        ->assertExitCode(0);

    Queue::assertPushedTimes(RefreshInstagramMediaFeedJob::class, 1);
});

it('queues an Instagram access token refresh job', function () {
    Queue::fake([RefreshInstagramAccessTokenJob::class]);

    $this->artisan('instagram:access-token:refresh')
        ->expectsOutput('Instagram access token refresh job queued.')
        ->assertExitCode(0);

    Queue::assertPushedTimes(RefreshInstagramAccessTokenJob::class, 1);
});
