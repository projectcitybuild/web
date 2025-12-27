<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires server token', function () {
    $uuid = MinecraftUUID::random();

    $this->getJson("http://api.localhost/v3/players/$uuid")
        ->assertUnauthorized();

    $status = $this
        ->withServerToken()
        ->getJson("http://api.localhost/v3/players/$uuid")
        ->status();

    expect($status)->not->toEqual(401);
});

it('throws exception for invalid Minecraft UUID', function () {
    $this->withServerToken()
        ->getJson('http://api.localhost/v3/players/invalid')
        ->assertInvalid(['uuid']);
});

it('throws 404 if player does not exist', function () {
    $uuid = MinecraftUUID::random();

    $this->withServerToken()
        ->getJson("http://api.localhost/v3/players/$uuid")
        ->assertNotFound();
});

it('returns existing player', function () {
    $player = MinecraftPlayer::factory()->create();

    $this->withServerToken()
        ->getJson("http://api.localhost/v3/players/$player->uuid")
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('player_minecraft_id', $player->getKey())
            ->where('uuid', $player->uuid)
            ->where('alias', $player->alias)
            ->where('nickname', $player->nickname)
            ->where('created_at', $player->created_at->toISOString())
            ->where('updated_at', $player->updated_at->toISOString())
            ->etc()
        );
});
