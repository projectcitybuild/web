<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\MinecraftStats\Data\PlayerStatIncrement;
use App\Domains\PlayerOpElevations\Notifications\OpElevationStartNotification;
use App\Models\MinecraftPlayer;
use App\Models\PlayerOpElevation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
    Config::set('discord.webhook_op_elevation_channel', 'foo_bar');
});

it('requires server token', function () {
    $this->postJson('http://api.localhost/v3/server/op/grant')
        ->assertUnauthorized();

    $status = $this
        ->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant')
        ->status();

    expect($status)->not->toEqual(401);
});

it('throws exception for invalid Minecraft UUID', function () {
    $stats = PlayerStatIncrement::random();

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant', [
            'uuid' => 'invalid',
        ])
        ->assertInvalid(['uuid']);

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant', [
            'uuid' => MinecraftUUID::random()->trimmed(),
        ])
        ->assertValid(['uuid']);
});

it('throws exception for missing reason', function () {
    $stats = PlayerStatIncrement::random();

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant', [
            'uuid' => MinecraftUUID::random()->trimmed(),
        ])
        ->assertInvalid(['reason']);

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant', [
            'uuid' => MinecraftUUID::random()->trimmed(),
            'reason' => 'foo bar',
        ])
        ->assertValid(['reason']);
});

it('creates op elevation record', function () {
    $player = MinecraftPlayer::factory()->create();
    $reason = 'foo bar';

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant', [
            'uuid' => $player->uuid,
            'reason' => $reason,
        ])
        ->assertCreated();

    $this->assertDatabaseHas(PlayerOpElevation::tableName(), [
        'player_id' => $player->getKey(),
        'reason' => $reason,
    ]);
});

it('sends discord message', function () {
    $player = MinecraftPlayer::factory()->create();
    $reason = 'foo bar';

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant', [
            'uuid' => $player->uuid,
            'reason' => $reason,
        ]);

    Notification::assertSentOnDemand(OpElevationStartNotification::class);
});

it('throws if already elevated', function () {
    $player = MinecraftPlayer::factory()->create();
    PlayerOpElevation::factory()->active()->create([
        'player_id' => $player->getKey(),
    ]);

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant', [
            'uuid' => $player->uuid,
            'reason' => 'foo bar',
        ])
        ->assertConflict();
});

it('checks ended_at to determine active elevation', function () {
    $player = MinecraftPlayer::factory()->create();
    PlayerOpElevation::factory()->ended()->create([
        'player_id' => $player->getKey(),
    ]);
    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant', [
            'uuid' => $player->uuid,
            'reason' => 'foo bar',
        ])
        ->assertCreated();

    $player = MinecraftPlayer::factory()->create();
    PlayerOpElevation::factory()->active()->create([
        'player_id' => $player->getKey(),
    ]);
    $status = $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/op/grant', [
            'uuid' => $player->uuid,
            'reason' => 'foo bar',
        ])
        ->status();

    expect($status)->not->toBe(201);
});
