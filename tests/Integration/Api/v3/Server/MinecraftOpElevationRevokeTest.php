<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\MinecraftStats\Data\PlayerStatIncrement;
use App\Domains\PlayerOpElevations\Notifications\OpElevationEndNotification;
use App\Models\MinecraftPlayer;
use App\Models\PlayerOpElevation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
    Config::set('discord.webhook_op_elevation_channel', 'foo_bar');
});

it('requires server token', function () {
    $this->postJson('http://api.localhost/v3/server/op/revoke')
        ->assertUnauthorized();

    $status = $this
        ->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/revoke')
        ->status();

    expect($status)->not->toEqual(401);
});

it('throws exception for invalid Minecraft UUID', function () {
    $stats = PlayerStatIncrement::random();

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/revoke', [
            'uuid' => 'invalid',
        ])
        ->assertInvalid(['uuid']);

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/revoke', [
            'uuid' => MinecraftUUID::random()->trimmed(),
        ])
        ->assertValid(['uuid']);
});

it('updates op elevation record', function () {
    $this->freezeTime(function ($now) {
        $now->setMicroseconds(0);

        $player = MinecraftPlayer::factory()->create();
        $elevation = PlayerOpElevation::factory()->active()->create([
            'player_id' => $player->getKey(),
        ]);

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/op/revoke', [
                'uuid' => $player->uuid,
            ])
            ->assertOk();

        $elevation->refresh();
        expect($elevation->ended_at->equalTo($now))->toBeTrue();
    });
});

it('sends discord message', function () {
    $player = MinecraftPlayer::factory()->create();
    PlayerOpElevation::factory()->active()->create([
        'player_id' => $player->getKey(),
    ]);

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/revoke', [
            'uuid' => $player->uuid,
        ]);

    Notification::assertSentOnDemand(OpElevationEndNotification::class);
});

it('throws if not currently elevated', function () {
    $player = MinecraftPlayer::factory()->create();

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/revoke', [
            'uuid' => $player->uuid,
            'reason' => 'foo bar',
        ])
        ->assertNotFound();
});

it('checks ended_at to determine active elevation', function () {
    $player = MinecraftPlayer::factory()->create();
    PlayerOpElevation::factory()->active()->create([
        'player_id' => $player->getKey(),
    ]);
    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/revoke', [
            'uuid' => $player->uuid,
        ])
        ->assertOk();

    $player = MinecraftPlayer::factory()->create();
    PlayerOpElevation::factory()->ended()->create([
        'player_id' => $player->getKey(),
    ]);
    $status = $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/revoke', [
            'uuid' => $player->uuid,
        ])
        ->status();

    expect($status)->not->toBe(200);
});
