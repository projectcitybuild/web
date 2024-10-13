<?php

use App\Models\MinecraftPlayer;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->endpoint = 'api/v2/minecraft/telemetry/seen';
});

it('requires server token', function () {
    $body = ['uuid' => 'uuid', 'alias' => 'alias'];

    $this->post($this->endpoint, $body)
        ->assertUnauthorized();

    $status = $this->withServerToken()
        ->post($this->endpoint, $body)
        ->status();

    expect($status)->not->toEqual(401);
});

it('validates input', function () {
    $this->withServerToken()
        ->post(uri: $this->endpoint, data: [])
        ->assertInvalid(['alias', 'uuid']);

    $this->withServerToken()
        ->post(uri: $this->endpoint, data: [
            'uuid' => '069a79f444e94726a5befca90e38aaf5',
            'alias' => 'alias',
        ])
        ->assertOk();
});

it('updates last seen date', function () {
    $this->freezeTime(function (Carbon $time) {
        $uuid = '069a79f444e94726a5befca90e38aaf5';
        $oldTime = $time->copy()->subWeek();
        $player = MinecraftPlayer::factory()->create([
            'uuid' => $uuid,
            'last_seen_at' => $oldTime,
        ]);

        $this->assertDatabaseHas(
            table: MinecraftPlayer::tableName(),
            data: [
                'player_minecraft_id' => $player->getKey(),
                'uuid' => $uuid,
                'last_seen_at' => $oldTime,
            ],
        );

        $this->withServerToken()
            ->post(uri: $this->endpoint, data: [
                'uuid' => $uuid,
                'alias' => 'alias',
            ])
            ->assertOk();

        $this->assertDatabaseHas(
            table: MinecraftPlayer::tableName(),
            data: [
                'player_minecraft_id' => $player->getKey(),
                'uuid' => $uuid,
                'last_seen_at' => $time,
            ],
        );
    });
});

it('updates alias', function () {
    $this->freezeTime(function (Carbon $time) {
        $uuid = '069a79f444e94726a5befca90e38aaf5';
        $player = MinecraftPlayer::factory()->create([
            'uuid' => $uuid,
            'alias' => 'old_alias',
        ]);

        $this->assertDatabaseHas(
            table: MinecraftPlayer::tableName(),
            data: [
                'player_minecraft_id' => $player->getKey(),
                'uuid' => $uuid,
                'alias' => 'old_alias',
            ],
        );

        $this->withServerToken()
            ->post(uri: $this->endpoint, data: [
                'uuid' => $uuid,
                'alias' => 'new_alias',
            ])
            ->assertOk();

        $this->assertDatabaseHas(
            table: MinecraftPlayer::tableName(),
            data: [
                'player_minecraft_id' => $player->getKey(),
                'uuid' => $uuid,
                'alias' => 'new_alias',
            ],
        );
    });
});

it('creates new player for uuid', function () {
    $uuid = '069a79f444e94726a5befca90e38aaf5';
    $this->assertDatabaseMissing(
        table: MinecraftPlayer::tableName(),
        data: [
            'uuid' => $uuid,
        ],
    );

    $this->withServerToken()
        ->post(uri: $this->endpoint, data: [
            'uuid' => $uuid,
            'alias' => 'alias',
        ])
        ->assertOk();

    $this->assertDatabaseHas(
        table: MinecraftPlayer::tableName(),
        data: [
            'uuid' => $uuid,
            'alias' => 'alias',
        ],
    );
});
