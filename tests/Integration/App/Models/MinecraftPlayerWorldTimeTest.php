<?php

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerWorldTime;
use Illuminate\Database\QueryException;

it('stores game-mode times for a player in a world', function () {
    $player = MinecraftPlayer::factory()->create();

    $worldTime = MinecraftPlayerWorldTime::factory()->create([
        'player_id' => $player->id,
        'world_uuid' => '123e4567-e89b-12d3-a456-426614174000',
        'creative_time' => 120,
        'survival_time' => 3600,
        'adventure_time' => 45,
        'spectator_time' => 10,
    ]);

    expect($player->worldTimes()->first()?->is($worldTime))->toBeTrue();
    expect($worldTime->only([
        'creative_time',
        'survival_time',
        'adventure_time',
        'spectator_time',
    ]))->toBe([
        'creative_time' => 120,
        'survival_time' => 3600,
        'adventure_time' => 45,
        'spectator_time' => 10,
    ]);
});

it('keeps one world-time aggregate per player and world', function () {
    $player = MinecraftPlayer::factory()->create();
    $worldUuid = '123e4567-e89b-12d3-a456-426614174000';

    MinecraftPlayerWorldTime::factory()->create([
        'player_id' => $player->id,
        'world_uuid' => $worldUuid,
    ]);

    expect(fn () => MinecraftPlayerWorldTime::factory()->create([
        'player_id' => $player->id,
        'world_uuid' => $worldUuid,
    ]))->toThrow(QueryException::class);
});
