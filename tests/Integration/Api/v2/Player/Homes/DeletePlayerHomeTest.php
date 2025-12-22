<?php

use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;

it('deletes a home', function () {
    $player = MinecraftPlayer::factory()->create();
    $home = MinecraftHome::factory()->create([
        'player_id' => $player->getKey(),
    ]);

    $response = $this
        ->withServerToken()
        ->deleteJson("/api/v2/minecraft/player/{$player->uuid}/home/{$home->id}");

    $response->assertOk();
});

it('prevents deleting another players home', function () {
    $player = MinecraftPlayer::factory()->create();
    $otherPlayer = MinecraftPlayer::factory()->create();

    $home = MinecraftHome::factory()->create([
        'player_id' => $otherPlayer->getKey(),
    ]);

    $response = $this
        ->withServerToken()
        ->deleteJson("/api/v2/minecraft/player/{$player->uuid}/home/{$home->id}");

    $response->assertForbidden();
});
