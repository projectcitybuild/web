<?php

use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use App\Domains\PlayerMail\Jobs\DistributePlayerMailJob;
use App\Domains\PlayerMail\Jobs\RecoverPlayerMailBroadcastsJob;
use App\Models\PlayerMailBroadcast;
use Illuminate\Support\Facades\Queue;

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
