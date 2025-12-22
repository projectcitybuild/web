<?php

use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;

describe('validation', function() {
    it('fails if home name already exists for player', function () {
        $player = MinecraftPlayer::factory()->create();

        MinecraftHome::factory()->create([
            'player_id' => $player->getKey(),
            'name' => 'spawn',
        ]);

        $response = $this
            ->withServerToken()
            ->postJson("/api/v2/minecraft/player/{$player->uuid}/home", [
                'name' => 'spawn',
                'world' => 'world',
                'x' => 1,
                'y' => 2,
                'z' => 3,
                'pitch' => 4.0,
                'yaw' => 5.0,
            ]);

        $response->assertConflict();
    });

    it('does not fail if home name exists for other player', function () {
        $player = MinecraftPlayer::factory()->create();
        $otherPlayer = MinecraftPlayer::factory()->create();

        MinecraftHome::factory()->create([
            'player_id' => $otherPlayer->getKey(),
            'name' => 'spawn',
        ]);

        $response = $this
            ->withServerToken()
            ->postJson("/api/v2/minecraft/player/{$player->uuid}/home", [
                'name' => 'spawn',
                'world' => 'world',
                'x' => 1,
                'y' => 2,
                'z' => 3,
                'pitch' => 4.0,
                'yaw' => 5.0,
            ]);

        $response->assertCreated();
    });
});

it('creates a home for a player', function () {
    $player = MinecraftPlayer::factory()->create();

    $response = $this
        ->withServerToken()
        ->postJson("/api/v2/minecraft/player/{$player->uuid}/home", [
            'name' => 'spawn',
            'world' => 'world',
            'x' => 1,
            'y' => 2,
            'z' => 3,
            'pitch' => 4.0,
            'yaw' => 5.0,
        ]);

    $response
        ->assertCreated()
        ->assertJsonFragment([
            'name' => 'spawn',
            'player_id' => $player->getKey(),
            'world' => 'world',
            'x' => 1,
            'y' => 2,
            'z' => 3,
            'pitch' => '4.0', // Laravel treats float as string here
            'yaw' => '5.0',
        ]);

    expect(MinecraftHome::where('player_id', $player->getKey())->count())
        ->toBe(1);
});
