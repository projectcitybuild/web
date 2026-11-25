<?php

use App\Core\Domains\Instagram\InstagramMediaFeed;
use App\Core\Domains\Instagram\Jobs\RefreshInstagramAccessTokenJob;
use Illuminate\Console\Scheduling\Schedule;

it('fails when Instagram access token refresh is not configured', function () {
    config([
        'services.instagram.user_id' => null,
        'services.instagram.access_token' => null,
        'services.instagram.api_version' => 'v27.0',
    ]);

    expect(fn () => (new RefreshInstagramAccessTokenJob)->handle(app(InstagramMediaFeed::class)))
        ->toThrow(UnexpectedValueException::class, 'Instagram access token refresh is not configured.');
});

it('schedules Instagram access token refresh weekly on one scheduler', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => $event->attributes['admin_title'] === 'Refresh Instagram access token');

    expect($event)->not->toBeNull()
        ->and($event->attributes['admin_subtitle'])->toBe('instagram.access-token.refresh')
        ->and($event->expression)->toBe('0 0 * * 0')
        ->and($event->timezone)->toBe('UTC')
        ->and($event->withoutOverlapping)->toBeTrue()
        ->and($event->onOneServer)->toBeTrue();
});
