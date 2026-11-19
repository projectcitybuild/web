<?php

use App\Domains\HealthCheck\Jobs\MailCanaryJob;
use App\Domains\HealthCheck\Jobs\QueueWorkerHeartbeatJob;
use Illuminate\Console\Scheduling\Schedule;

it('schedules the queue heartbeat every five minutes', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => $event->description === QueueWorkerHeartbeatJob::class);

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('*/5 * * * *');
});

it('schedules the mail canary once daily at 09:00 UTC on one scheduler', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => $event->description === MailCanaryJob::class);

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('0 9 * * *')
        ->and($event->timezone)->toBe('UTC')
        ->and($event->onOneServer)->toBeTrue();
});
