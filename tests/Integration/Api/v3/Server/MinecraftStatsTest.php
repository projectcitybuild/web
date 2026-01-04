<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\MinecraftStats\Data\PlayerStatIncrement;
use App\Domains\MinecraftStats\Jobs\IncrementPlayerStatsJob;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Queue;

describe('endpoint', function () {
    beforeEach(function () {
        Queue::fake();
    });

    it('requires server token', function () {
        $this->postJson('http://api.localhost/v3/server/stats')
            ->assertUnauthorized();

        $status = $this
            ->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats')
            ->status();

        expect($status)->not->toEqual(401);

        Queue::assertNothingPushed();
    });

    it('throws exception for invalid Minecraft UUID', function () {
        $stats = PlayerStatIncrement::random();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats', [
                'players' => [
                    'invalid' => $stats->toArray(),
                ],
            ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'invalid is not a valid Minecraft UUID',
                'errors' => [
                    [
                        'invalid is not a valid Minecraft UUID',
                    ],
                ],
            ]);

        Queue::assertNothingPushed();
    });

    it('dispatches stat increment job for each uuid', function () {
        $uuid1 = MinecraftUUID::random();
        $uuid2 = MinecraftUUID::random();
        $stats1 = PlayerStatIncrement::random();
        $stats2 = PlayerStatIncrement::random();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats', [
                'players' => [
                    $uuid1->trimmed() => $stats1->toArray(),
                    $uuid2->trimmed() => $stats2->toArray(),
                ],
            ])
            ->assertOk();

        Queue::assertPushed(function (IncrementPlayerStatsJob $job) use ($uuid1, $stats1) {
            return $job->uuid == $uuid1 && $job->increment == $stats1;
        });
        Queue::assertPushed(function (IncrementPlayerStatsJob $job) use ($uuid2, $stats2) {
            return $job->uuid == $uuid2 && $job->increment == $stats2;
        });
    });
});

describe('job', function () {
    it('increments present fields', function () {
        $this->freezeTime(function ($now) {
            $now->setMicroseconds(0);

            $increment = new PlayerStatIncrement(
                afkTime: 1,
                blocksPlaced: 2,
                blocksDestroyed: 3,
                blocksTravelled: 4,
            );
            $player = MinecraftPlayer::factory()->create([
                'afk_time' => 1,
                'blocks_placed' => 2,
                'blocks_destroyed' => 3,
                'blocks_travelled' => 4,
            ]);
            $job = new IncrementPlayerStatsJob(new MinecraftUUID($player->uuid), $increment);
            $job->handle();

            $player->refresh();
            expect($player->afk_time)->toEqual(2);
            expect($player->blocks_placed)->toEqual(4);
            expect($player->blocks_destroyed)->toEqual(6);
            expect($player->blocks_travelled)->toEqual(8);
            expect($player->updated_at)->toEqual($now);
        });
    });

    it('does not update missing fields', function () {
        $player = MinecraftPlayer::factory()->create();
        $prev = $player->replicate();

        $increment = new PlayerStatIncrement;
        $job = new IncrementPlayerStatsJob(new MinecraftUUID($player->uuid), $increment);
        $job->handle();

        $player->refresh();
        expect($player->afk_time)->toEqual($prev->afk_time);
        expect($player->blocks_placed)->toEqual($prev->blocks_placed);
        expect($player->blocks_destroyed)->toEqual($prev->blocks_destroyed);
        expect($player->blocks_travelled)->toEqual($prev->blocks_travelled);
    });
});
