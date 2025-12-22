<?php

use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;

it('shows a home owned by the player', function () {
    $player = MinecraftPlayer::factory()->create();
    $home = MinecraftHome::factory()->create([
        'player_id' => $player->getKey(),
    ]);

    $response = $this
        ->withServerToken()
        ->getJson("/api/v2/minecraft/player/{$player->uuid}/home/{$home->id}");

    $response
        ->assertOk()
        ->assertJsonFragment([
            'id' => $home->id,
            'name' => $home->name,
        ]);
});

it('returns 403 when accessing another players home', function () {
    $player = MinecraftPlayer::factory()->create();
    $otherPlayer = MinecraftPlayer::factory()->create();

    $home = MinecraftHome::factory()->create([
        'player_id' => $otherPlayer->getKey(),
    ]);

    $response = $this
        ->withServerToken()
        ->getJson("/api/v2/minecraft/player/{$player->uuid}/home/{$home->getKey()}");

    $response->assertForbidden();
});
