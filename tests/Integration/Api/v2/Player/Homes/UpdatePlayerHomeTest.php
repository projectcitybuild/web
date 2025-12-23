<?php

use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;

describe('validation', function () {
    it('fails if home name already exists for player', function () {
        $player = MinecraftPlayer::factory()->create();

        $home = MinecraftHome::factory()->create([
            'player_id' => $player->getKey(),
            'name' => 'spawn',
        ]);
        MinecraftHome::factory()->create([
            'player_id' => $player->getKey(),
            'name' => 'updated_spawn',
        ]);

        $response = $this
            ->withServerToken()
            ->putJson("/api/v2/minecraft/player/{$player->uuid}/home/{$home->getKey()}", [
                'name' => 'updated_spawn',
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

        $home = MinecraftHome::factory()->create([
            'player_id' => $player->getKey(),
            'name' => 'spawn',
        ]);
        MinecraftHome::factory()->create([
            'player_id' => $otherPlayer->getKey(),
            'name' => 'updated_spawn',
        ]);

        $response = $this
            ->withServerToken()
            ->putJson("/api/v2/minecraft/player/{$player->uuid}/home/{$home->getKey()}", [
                'name' => 'updated_spawn',
                'world' => 'world',
                'x' => 1,
                'y' => 2,
                'z' => 3,
                'pitch' => 4.0,
                'yaw' => 5.0,
            ]);

        $response->assertOk();
    });

    it('does not fail if home name is same as before', function () {
        $player = MinecraftPlayer::factory()->create();

        $home = MinecraftHome::factory()->create([
            'player_id' => $player->getKey(),
            'name' => 'spawn',
        ]);

        $response = $this
            ->withServerToken()
            ->putJson("/api/v2/minecraft/player/{$player->uuid}/home/{$home->getKey()}", [
                'name' => 'spawn',
                'world' => 'world',
                'x' => 1,
                'y' => 2,
                'z' => 3,
                'pitch' => 4.0,
                'yaw' => 5.0,
            ]);

        $response->assertOk();
    });
});

it('updates a home', function () {
    $player = MinecraftPlayer::factory()->create();
    $home = MinecraftHome::factory()->create([
        'player_id' => $player->getKey(),
        'name' => 'old_name',
    ]);

    $response = $this
        ->withServerToken()
        ->putJson("/api/v2/minecraft/player/{$player->uuid}/home/{$home->getKey()}", [
            'name' => 'new_name',
            'world' => 'new_world',
            'x' => 10,
            'y' => 11,
            'z' => 12,
            'pitch' => 13.0,
            'yaw' => 14.0,
        ]);

    $response
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'new_name',
            'player_id' => $player->getKey(),
            'world' => 'new_world',
            'x' => 10,
            'y' => 11,
            'z' => 12,
            'pitch' => '13.0', // Laravel treats float as string here
            'yaw' => '14.0',
        ]);
});
