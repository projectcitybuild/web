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

it('updates last_seen_at', function () {
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
        expect($player->play_time )->toEqual(30);
    });
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
        expect(MinecraftPlayerSession::where('player_id', $player->getKey())->exists())
            ->toBeFalse();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/end', [
                'uuid' => $player->uuid,
                'session_seconds' => 20,
            ]);

        $player->refresh();
        expect($player->sessions)->toEqual(1);
        expect(MinecraftPlayerSession::where('player_id', $player->getKey())->exists())
            ->toBeTrue();
    });
});
