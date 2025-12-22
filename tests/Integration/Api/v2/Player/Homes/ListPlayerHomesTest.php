<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;

it('lists homes for a player', function () {
    $player = MinecraftPlayer::factory()->create();
    MinecraftHome::factory()->count(3)->create([
        'player_id' => $player->getKey(),
    ]);

    $response = $this
        ->withServerToken()
        ->getJson("/api/v2/minecraft/player/{$player->uuid}/home");

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'links',
        ])
        ->assertJsonCount(3, 'data');
});

it('returns 404 if player does not exist when listing homes', function () {
    $uuid = MinecraftUUID::random();
    $response = $this
        ->withServerToken()
        ->getJson("/api/v2/minecraft/player/{$uuid->trimmed()}/home");

    $response->assertNotFound();
});
