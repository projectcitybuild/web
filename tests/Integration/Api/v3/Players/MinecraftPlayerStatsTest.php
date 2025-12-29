<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires server token', function () {
    $uuid = MinecraftUUID::random();

    $this->postJson("http://api.localhost/v3/players/$uuid/stats")
        ->assertUnauthorized();

    $status = $this
        ->withServerToken()
        ->postJson("http://api.localhost/v3/players/$uuid/stats")
        ->status();

    expect($status)->not->toEqual(401);
});

it('throws exception for invalid Minecraft UUID', function () {
    $this->withServerToken()
        ->postJson('http://api.localhost/v3/players/invalid/stats')
        ->assertInvalid(['uuid']);
});

it('throws 404 if player does not exist', function () {
    $uuid = MinecraftUUID::random();

    $this->withServerToken()
        ->postJson("http://api.localhost/v3/players/$uuid/stats")
        ->assertNotFound();
});

it('increments present fields', function () {
    $this->freezeTime(function ($now) {
        $now->setMicroseconds(0);
        $player = MinecraftPlayer::factory()->create([
            'blocks_placed' => 1,
            'blocks_destroyed' => 2,
            'blocks_travelled' => 3,
            'afk_time' => 4,
        ]);

        $this->withServerToken()
            ->postJson("http://api.localhost/v3/players/$player->uuid/stats", [
                'blocks_placed' => 1,
                'blocks_destroyed' => 2,
                'blocks_travelled' => 3,
                'afk_time' => 4,
            ])
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('blocks_placed', 2)
                ->where('blocks_destroyed', 4)
                ->where('blocks_travelled', 6)
                ->where('afk_time', 8)
                ->where('updated_at', $now->toISOString())
                ->etc()
            );
    });
});

it('does not update missing fields', function () {
    $player = MinecraftPlayer::factory()->create();
    $this->withServerToken()
        ->postJson("http://api.localhost/v3/players/$player->uuid/stats", [])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('blocks_placed', $player->blocks_placed)
            ->where('blocks_destroyed', $player->blocks_destroyed)
            ->where('blocks_travelled', $player->blocks_travelled)
            ->where('afk_time', $player->afk_time)
            ->where('updated_at', $player->updated_at->toISOString())
            ->etc()
        );
});
