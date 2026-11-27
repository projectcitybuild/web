<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;

it('requires server token', function () {
    $this->postJson('http://api.localhost/v3/server/connection/end')
        ->assertUnauthorized();

    $status = $this
        ->withServerToken()
        ->postJson('http://api.localhost/v3/server/connection/end', [
            'uuid' => MinecraftUUID::random()->trimmed(),
        ])
        ->status();

    expect($status)->not->toEqual(401);
});

it('throws exception for invalid Minecraft UUID', function () {
    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/connection/end', [
            'uuid' => 'invalid',
        ])
        ->assertInvalid(['uuid']);
});

it('updates last_seen_at from null', function () {
    $this->freezeTime(function ($now) {
        $now->setMicroseconds(0);

        $player = MinecraftPlayer::factory()->create(['last_seen_at' => null]);
        expect($player->last_seen_at)->toEqual(null);

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/end', [
                'uuid' => $player->uuid,
                'session_seconds' => 0,
            ]);

        $player->refresh();
        expect($player->last_seen_at)->toEqual($now);
    });
});

it('updates last_seen_at from existing value', function () {
    $this->freezeTime(function ($now) {
        $now->setMicroseconds(0);

        $prev = $now->copy()->subWeek()->setMicroseconds(0);
        $player = MinecraftPlayer::factory()->create(['last_seen_at' => $prev]);
        expect($player->last_seen_at)->toEqual($prev);

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/end', [
                'uuid' => $player->uuid,
                'session_seconds' => 0,
            ]);

        $player->refresh();
        expect($player->last_seen_at)->toEqual($now);
    });
});

it('updates play time', function () {
    $this->freezeTime(function ($now) {
        $now->setMicroseconds(0);

        $player = MinecraftPlayer::factory()->create(['play_time' => 0]);
        expect($player->play_time)->toEqual(0);

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/end', [
                'uuid' => $player->uuid,
                'session_seconds' => 30,
            ]);

        $player->refresh();
        expect($player->play_time)->toEqual(30);
    });
});

it('updates the last location from logout coordinates', function () {
    $player = MinecraftPlayer::factory()->create();

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/connection/end', [
            'uuid' => $player->uuid,
            'session_seconds' => 0,
            'world_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'x' => 12.5,
            'y' => 64,
            'z' => -8.25,
            'pitch' => 30,
            'yaw' => 180,
        ])
        ->assertOk();

    $player->refresh();
    expect([
        $player->last_location_world_uuid,
        $player->last_location_x,
        $player->last_location_y,
        $player->last_location_z,
        $player->last_location_pitch,
        $player->last_location_yaw,
    ])->toEqual([
        '123e4567-e89b-12d3-a456-426614174000',
        12.5,
        64.0,
        -8.25,
        30.0,
        180.0,
    ]);
});

it('keeps the previous location when logout coordinates are omitted', function () {
    $player = MinecraftPlayer::factory()->create([
        'last_location_world_uuid' => '123e4567-e89b-12d3-a456-426614174000',
        'last_location_x' => 1.5,
        'last_location_y' => 70,
        'last_location_z' => 3.25,
        'last_location_pitch' => 45,
        'last_location_yaw' => 90,
    ]);

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/connection/end', [
            'uuid' => $player->uuid,
            'session_seconds' => 0,
        ])
        ->assertOk();

    $player->refresh();
    expect([
        $player->last_location_world_uuid,
        $player->last_location_x,
        $player->last_location_y,
        $player->last_location_z,
        $player->last_location_pitch,
        $player->last_location_yaw,
    ])->toEqual([
        '123e4567-e89b-12d3-a456-426614174000',
        1.5,
        70.0,
        3.25,
        45.0,
        90.0,
    ]);
});

it('requires all logout coordinate values when any are provided', function () {
    $player = MinecraftPlayer::factory()->create();

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/connection/end', [
            'uuid' => $player->uuid,
            'session_seconds' => 0,
            'x' => 12.5,
        ])
        ->assertInvalid(['world_uuid', 'y', 'z', 'pitch', 'yaw']);

    $player->refresh();
    expect([
        $player->last_location_world_uuid,
        $player->last_location_x,
        $player->last_location_y,
        $player->last_location_z,
        $player->last_location_pitch,
        $player->last_location_yaw,
    ])->toEqual([null, null, null, null, null, null]);
});

it('creates session data if session time at least 20 seconds', function () {
    $this->freezeTime(function ($now) {
        $now->setMicroseconds(0);

        $player = MinecraftPlayer::factory()->create(['sessions' => 0]);
        expect($player->sessions)->toEqual(0);

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/end', [
                'uuid' => $player->uuid,
                'session_seconds' => 0,
            ]);

        $player->refresh();
        expect($player->sessions)->toEqual(0);
        expect(MinecraftPlayerSession::where('player_id', $player->id)->exists())
            ->toBeFalse();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/end', [
                'uuid' => $player->uuid,
                'session_seconds' => 20,
            ]);

        $player->refresh();
        expect($player->sessions)->toEqual(1);
        expect(MinecraftPlayerSession::where('player_id', $player->id)->exists())
            ->toBeTrue();
    });
});
