<?php

use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;

it('shows a home owned by the player', function () {
    $player = MinecraftPlayer::factory()->create();
    $home = MinecraftHome::factory()->create([
        'player_id' => $player->id,
    ]);

    $response = $this
        ->withServerToken()
        ->getJson("http://api.localhost/v3/players/{$player->uuid}/homes/{$home->id}");

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
        'player_id' => $otherPlayer->id,
    ]);

    $response = $this
        ->withServerToken()
        ->getJson("http://api.localhost/v3/players/{$player->uuid}/homes/{$home->id}");

    $response->assertForbidden();
});
