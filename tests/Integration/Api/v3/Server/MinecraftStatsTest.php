<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\MinecraftStats\Data\PlayerStatIncrement;
use App\Domains\MinecraftStats\Jobs\IncrementPlayerStatsJob;
use App\Models\MinecraftPlayer;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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

    it('accepts and maps all newly collected statistics', function () {
        $uuid = MinecraftUUID::random();
        $increment = new PlayerStatIncrement(
            afkTime: 1,
            blocksPlaced: 2,
            blocksDestroyed: 3,
            blocksTravelled: 4,
            deaths: 5,
            mobKills: 6,
            playerKills: 7,
        );

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats', [
                'players' => [
                    $uuid->trimmed() => $increment->toArray(),
                ],
            ])
            ->assertOk();

        Queue::assertPushed(function (IncrementPlayerStatsJob $job) use ($uuid, $increment) {
            return $job->uuid == $uuid && $job->increment == $increment;
        });
    });

    it('rejects negative and non-integer stat increments', function () {
        foreach (['blocks_travelled', 'deaths', 'mob_kills', 'player_kills'] as $stat) {
            foreach ([-1, 1.5] as $value) {
                $this->withServerToken()
                    ->postJson('http://api.localhost/v3/server/stats', [
                        'players' => [
                            MinecraftUUID::random()->trimmed() => [$stat => $value],
                        ],
                    ])
                    ->assertUnprocessable();
            }
        }

        Queue::assertNothingPushed();
    });
});

it('bulk inserts stat jobs for multiple players', function () {
    Config::set('queue.default', 'database');

    $jobInsertQueries = 0;
    DB::listen(function (QueryExecuted $query) use (&$jobInsertQueries) {
        if (str_starts_with(strtolower(trim($query->sql)), 'insert into `jobs`')) {
            $jobInsertQueries++;
        }
    });

    $uuid1 = MinecraftUUID::random();
    $uuid2 = MinecraftUUID::random();

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/stats', [
            'players' => [
                $uuid1->trimmed() => ['afk_time' => 1],
                $uuid2->trimmed() => ['blocks_placed' => 2],
            ],
        ])
        ->assertOk();

    expect($jobInsertQueries)->toBe(1);
    expect(DB::table('jobs')->count())->toBe(2);
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
                deaths: 5,
                mobKills: 6,
                playerKills: 7,
            );
            $player = MinecraftPlayer::factory()->create([
                'afk_time' => 1,
                'blocks_placed' => 2,
                'blocks_destroyed' => 3,
                'blocks_travelled' => 4,
                'deaths' => 5,
                'mob_kills' => 6,
                'player_kills' => 7,
            ]);
            $job = new IncrementPlayerStatsJob(new MinecraftUUID($player->uuid), $increment);
            $job->handle();

            $player->refresh();
            expect($player->afk_time)->toEqual(2);
            expect($player->blocks_placed)->toEqual(4);
            expect($player->blocks_destroyed)->toEqual(6);
            expect($player->blocks_travelled)->toEqual(8);
            expect($player->deaths)->toEqual(10);
            expect($player->mob_kills)->toEqual(12);
            expect($player->player_kills)->toEqual(14);
            expect($player->updated_at)->toEqual($now);
        });
    });

    it('accumulates multiple updates', function () {
        $player = MinecraftPlayer::factory()->create([
            'deaths' => 2,
            'mob_kills' => 3,
            'player_kills' => 4,
        ]);
        $increment = new PlayerStatIncrement(deaths: 1, mobKills: 2, playerKills: 3);
        $job = new IncrementPlayerStatsJob(new MinecraftUUID($player->uuid), $increment);

        $job->handle();
        $job->handle();

        $player->refresh();
        expect($player->deaths)->toEqual(4);
        expect($player->mob_kills)->toEqual(7);
        expect($player->player_kills)->toEqual(10);
    });

    it('does not update missing fields', function () {
        $player = MinecraftPlayer::factory()->create([
            'afk_time' => 11,
            'blocks_placed' => 12,
            'blocks_destroyed' => 13,
            'blocks_travelled' => 14,
            'deaths' => 15,
            'mob_kills' => 16,
            'player_kills' => 17,
        ]);
        $prev = $player->replicate();

        $increment = new PlayerStatIncrement;
        $job = new IncrementPlayerStatsJob(new MinecraftUUID($player->uuid), $increment);
        $job->handle();

        $player->refresh();
        expect($player->afk_time)->toEqual($prev->afk_time);
        expect($player->blocks_placed)->toEqual($prev->blocks_placed);
        expect($player->blocks_destroyed)->toEqual($prev->blocks_destroyed);
        expect($player->blocks_travelled)->toEqual($prev->blocks_travelled);
        expect($player->deaths)->toEqual($prev->deaths);
        expect($player->mob_kills)->toEqual($prev->mob_kills);
        expect($player->player_kills)->toEqual($prev->player_kills);
    });
});
