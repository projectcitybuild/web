<?php

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerKill;
use App\Models\MinecraftPlayerWorldTime;
use Illuminate\Support\Str;

describe('player kill ingestion', function () {
    it('requires a server token', function () {
        $this->postJson('http://api.localhost/v3/server/stats/kills', ['kills' => []])
            ->assertUnauthorized();
    });

    it('stores authorized kills with and without a weapon', function () {
        $attacker = MinecraftPlayer::factory()->create();
        $victim = MinecraftPlayer::factory()->create();
        $killedAt = now()->subMinute();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats/kills', [
                'kills' => [
                    [
                        'event_id' => (string) Str::uuid(),
                        'attacker_uuid' => $attacker->uuid,
                        'victim_uuid' => $victim->uuid,
                        'weapon' => 'minecraft:diamond_sword',
                        'killed_at' => $killedAt->toIso8601String(),
                    ],
                    [
                        'event_id' => (string) Str::uuid(),
                        'attacker_uuid' => $attacker->uuid,
                        'victim_uuid' => $victim->uuid,
                        'killed_at' => $killedAt->toIso8601String(),
                    ],
                ],
            ])
            ->assertNoContent();

        expect(MinecraftPlayerKill::query()->count())->toBe(2);
        $storedKill = MinecraftPlayerKill::query()->first();
        expect($storedKill->player_id)->toBe($attacker->id);
        expect($storedKill->victim_player_id)->toBe($victim->id);
        expect($storedKill->weapon)->toBe('minecraft:diamond_sword');
        expect($storedKill->killed_at->toDateTimeString())->toBe($killedAt->toDateTimeString());
        expect(MinecraftPlayerKill::query()->latest('id')->first()->weapon)->toBeNull();
    });

    it('rejects invalid identifiers, timestamps, and unknown player records atomically', function () {
        $attacker = MinecraftPlayer::factory()->create();
        $victim = MinecraftPlayer::factory()->create();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats/kills', [
                'kills' => [
                    [
                        'event_id' => (string) Str::uuid(),
                        'attacker_uuid' => 'invalid',
                        'victim_uuid' => $victim->uuid,
                        'killed_at' => 'not-a-timestamp',
                    ],
                ],
            ])
            ->assertUnprocessable();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats/kills', [
                'kills' => [
                    [
                        'event_id' => (string) Str::uuid(),
                        'attacker_uuid' => $attacker->uuid,
                        'victim_uuid' => Str::uuid()->toString(),
                        'killed_at' => now()->toIso8601String(),
                    ],
                ],
            ])
            ->assertUnprocessable();

        expect(MinecraftPlayerKill::query()->count())->toBe(0);
    });

    it('rejects invalid weapons and does not partially insert a batch', function () {
        $attacker = MinecraftPlayer::factory()->create();
        $victim = MinecraftPlayer::factory()->create();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats/kills', [
                'kills' => [
                    [
                        'event_id' => (string) Str::uuid(),
                        'attacker_uuid' => $attacker->uuid,
                        'victim_uuid' => $victim->uuid,
                        'weapon' => str_repeat('x', 101),
                        'killed_at' => now()->toIso8601String(),
                    ],
                ],
            ])
            ->assertUnprocessable();

        expect(MinecraftPlayerKill::query()->count())->toBe(0);
    });

    it('does not insert valid kill events when another batch item is malformed', function () {
        $attacker = MinecraftPlayer::factory()->create();
        $victim = MinecraftPlayer::factory()->create();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats/kills', [
                'kills' => [
                    [
                        'event_id' => (string) Str::uuid(),
                        'attacker_uuid' => $attacker->uuid,
                        'victim_uuid' => $victim->uuid,
                        'killed_at' => now()->toIso8601String(),
                    ],
                    [
                        'event_id' => (string) Str::uuid(),
                        'attacker_uuid' => $attacker->uuid,
                        'victim_uuid' => $victim->uuid,
                        'killed_at' => 'invalid timestamp',
                    ],
                ],
            ])
            ->assertUnprocessable();

        expect(MinecraftPlayerKill::query()->count())->toBe(0);
    });

    it('does not duplicate a kill when the same event is retried', function () {
        $attacker = MinecraftPlayer::factory()->create();
        $victim = MinecraftPlayer::factory()->create();
        $eventId = (string) Str::uuid();
        $payload = [
            'kills' => [[
                'event_id' => $eventId,
                'attacker_uuid' => $attacker->uuid,
                'victim_uuid' => $victim->uuid,
                'weapon' => null,
                'killed_at' => now()->toIso8601String(),
            ]],
        ];

        $this->withServerToken()->postJson('http://api.localhost/v3/server/stats/kills', $payload)
            ->assertNoContent();
        $this->withServerToken()->postJson('http://api.localhost/v3/server/stats/kills', $payload)
            ->assertNoContent();

        expect(MinecraftPlayerKill::query()->where('event_id', $eventId)->count())->toBe(1);
    });
});

describe('player world-time ingestion', function () {
    it('requires a server token', function () {
        $this->postJson('http://api.localhost/v3/server/stats/world-times', ['increments' => []])
            ->assertUnauthorized();
    });

    it('increments the correct mode counters without replacing existing totals', function () {
        $player = MinecraftPlayer::factory()->create();
        $worldUuid = (string) Str::uuid();
        $worldTime = MinecraftPlayerWorldTime::factory()->create([
            'player_id' => $player->id,
            'world_uuid' => $worldUuid,
            'survival_time' => 80,
            'creative_time' => 30,
            'adventure_time' => 40,
            'spectator_time' => 50,
        ]);

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats/world-times', [
                'increments' => [
                    ['player_uuid' => $player->uuid, 'world_uuid' => $worldUuid, 'game_mode' => 'survival', 'duration_seconds' => 5],
                    ['player_uuid' => $player->uuid, 'world_uuid' => $worldUuid, 'game_mode' => 'creative', 'duration_seconds' => 6],
                    ['player_uuid' => $player->uuid, 'world_uuid' => $worldUuid, 'game_mode' => 'adventure', 'duration_seconds' => 7],
                    ['player_uuid' => $player->uuid, 'world_uuid' => $worldUuid, 'game_mode' => 'spectator', 'duration_seconds' => 8],
                ],
            ])
            ->assertNoContent();

        expect($worldTime->refresh()->only([
            'survival_time',
            'creative_time',
            'adventure_time',
            'spectator_time',
        ]))->toBe([
            'survival_time' => 85,
            'creative_time' => 36,
            'adventure_time' => 47,
            'spectator_time' => 58,
        ]);
    });

    it('rejects invalid modes, negative durations, and unknown player records', function () {
        $player = MinecraftPlayer::factory()->create();
        $worldUuid = (string) Str::uuid();

        foreach ([
            ['player_uuid' => $player->uuid, 'world_uuid' => $worldUuid, 'game_mode' => 'hardcore', 'duration_seconds' => 1],
            ['player_uuid' => $player->uuid, 'world_uuid' => $worldUuid, 'game_mode' => 'survival', 'duration_seconds' => -1],
            ['player_uuid' => Str::uuid()->toString(), 'world_uuid' => $worldUuid, 'game_mode' => 'survival', 'duration_seconds' => 1],
        ] as $increment) {
            $this->withServerToken()
                ->postJson('http://api.localhost/v3/server/stats/world-times', [
                    'increments' => [$increment],
                ])
                ->assertUnprocessable();
        }

        expect(MinecraftPlayerWorldTime::query()->count())->toBe(0);
    });

    it('rejects a malformed batch before applying any increments', function () {
        $player = MinecraftPlayer::factory()->create();
        $worldUuid = (string) Str::uuid();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/stats/world-times', [
                'increments' => [
                    ['player_uuid' => $player->uuid, 'world_uuid' => $worldUuid, 'game_mode' => 'survival', 'duration_seconds' => 10],
                    ['player_uuid' => $player->uuid, 'world_uuid' => 'invalid', 'game_mode' => 'creative', 'duration_seconds' => 20],
                ],
            ])
            ->assertUnprocessable();

        expect(MinecraftPlayerWorldTime::query()->count())->toBe(0);
    });
});
