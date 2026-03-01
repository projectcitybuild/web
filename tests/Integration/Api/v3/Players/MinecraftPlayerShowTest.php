<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;
use Illuminate\Testing\Fluent\AssertableJson;

it('throws exception for invalid Minecraft UUID', function () {
    $this->getJson('http://api.localhost/v3/players/invalid')
        ->assertInvalid(['uuid']);
});

it('throws 404 if player does not exist', function () {
    $uuid = MinecraftUUID::random();

    $this->getJson("http://api.localhost/v3/players/$uuid")
        ->assertNotFound();
});

it('returns existing player', function () {
    $player = MinecraftPlayer::factory()->create([
        'fly_speed' => 1.5,
        'walk_speed' => 2.5,
    ]);
    $this->getJson("http://api.localhost/v3/players/$player->uuid")
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('id', $player->id)
            ->where('uuid', $player->uuid)
            ->where('alias', $player->alias)
            ->where('nickname', $player->nickname)
            ->where('fly_speed', $player->fly_speed)
            ->where('walk_speed', $player->walk_speed)
            ->where('sessions', $player->sessions)
            ->where('play_time', $player->play_time)
            ->where('afk_time', $player->afk_time)
            ->where('blocks_placed', $player->blocks_placed)
            ->where('blocks_destroyed', $player->blocks_destroyed)
            ->where('blocks_travelled', $player->blocks_travelled)
            ->where('created_at', $player->created_at->toISOString())
            ->where('updated_at', $player->updated_at->toISOString())
            ->etc()
        );
});
