<?php

use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use App\Domains\PlayerMail\Jobs\DistributePlayerMailJob;
use App\Domains\PlayerMail\Jobs\RecoverPlayerMailBroadcastsJob;
use App\Models\PlayerMailBroadcast;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Queue;

it('schedules recovery for stale player mail broadcasts every minute', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => $event->attributes['admin_title'] === 'Recover player mail broadcasts');

    expect($event)->not->toBeNull()
        ->and($event->attributes['admin_subtitle'])->toBe('player-mail.broadcast-recovery')
        ->and($event->expression)->toBe('* * * * *')
        ->and($event->withoutOverlapping)->toBeTrue();
});

it('dispatches distribution jobs for stale queued and processing broadcasts', function () {
    $this->travelTo('2026-11-23 12:00:00');

    $queuedBroadcast = PlayerMailBroadcast::factory()->create([
        'status' => PlayerMailBroadcastStatus::QUEUED,
        'last_player_id' => 17,
        'updated_at' => now()->subMinutes(6),
    ]);
    $processingBroadcast = PlayerMailBroadcast::factory()->create([
        'status' => PlayerMailBroadcastStatus::PROCESSING,
        'last_player_id' => 25,
        'updated_at' => now()->subMinutes(6),
    ]);
    PlayerMailBroadcast::factory()->create([
        'status' => PlayerMailBroadcastStatus::QUEUED,
        'updated_at' => now()->subMinutes(4),
    ]);
    PlayerMailBroadcast::factory()->create([
        'status' => PlayerMailBroadcastStatus::COMPLETED,
        'updated_at' => now()->subMinutes(6),
    ]);
    Queue::fake([DistributePlayerMailJob::class]);

    (new RecoverPlayerMailBroadcastsJob)->handle();

    Queue::assertPushed(DistributePlayerMailJob::class, 2);
    Queue::assertPushed(
        DistributePlayerMailJob::class,
        fn (DistributePlayerMailJob $job): bool => $job->broadcastId === $queuedBroadcast->id
            && $job->expectedLastPlayerId === 17,
    );
    Queue::assertPushed(
        DistributePlayerMailJob::class,
        fn (DistributePlayerMailJob $job): bool => $job->broadcastId === $processingBroadcast->id
            && $job->expectedLastPlayerId === 25,
    );
});
