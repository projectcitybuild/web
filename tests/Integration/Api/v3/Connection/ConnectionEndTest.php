<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;

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

        $prev = now()->subWeek()->setMicroseconds(0);
        $player = MinecraftPlayer::factory()->create(['last_seen_at' => $prev]);
        expect($player->last_seen_at)->toEqual($prev);

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/end', [
                'uuid' => $player->uuid,
            ]);

        $player->refresh();
        expect($player->last_seen_at)->toEqual($now);
    });
});
