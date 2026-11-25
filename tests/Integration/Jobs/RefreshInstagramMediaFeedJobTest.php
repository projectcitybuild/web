<?php

use App\Core\Domains\Instagram\InstagramMediaFeed;
use App\Core\Domains\Instagram\Jobs\RefreshInstagramMediaFeedJob;
use Illuminate\Console\Scheduling\Schedule;

it('fails when Instagram media refresh is not configured', function () {
    config([
        'services.instagram.user_id' => null,
        'services.instagram.access_token' => null,
        'services.instagram.api_version' => 'v27.0',
    ]);

    expect(fn () => (new RefreshInstagramMediaFeedJob)->handle(app(InstagramMediaFeed::class)))
        ->toThrow(UnexpectedValueException::class, 'Instagram media feed is not configured.');
});

it('schedules Instagram media refresh every thirty minutes on one scheduler', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => $event->attributes['admin_title'] === 'Refresh Instagram media feed');

    expect($event)->not->toBeNull()
        ->and($event->attributes['admin_subtitle'])->toBe('instagram.media-feed.refresh')
        ->and($event->expression)->toBe('*/30 * * * *')
        ->and($event->withoutOverlapping)->toBeTrue()
        ->and($event->onOneServer)->toBeTrue();
});
