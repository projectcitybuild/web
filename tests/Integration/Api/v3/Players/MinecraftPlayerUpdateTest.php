<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires server token', function () {
    $uuid = MinecraftUUID::random();

    $this->patchJson("http://api.localhost/v3/players/$uuid")
        ->assertUnauthorized();

    $status = $this
        ->withServerToken()
        ->patchJson("http://api.localhost/v3/players/$uuid", [
            'alias' => 'foobar',
        ])
        ->status();

    expect($status)->not->toEqual(401);
});

it('throws exception for invalid Minecraft UUID', function () {
    $this->withServerToken()
        ->patchJson('http://api.localhost/v3/players/invalid')
        ->assertInvalid(['uuid']);
});

it('throws 404 if player does not exist', function () {
    $uuid = MinecraftUUID::random();

    $this->withServerToken()
        ->patchJson("http://api.localhost/v3/players/$uuid")
        ->assertNotFound();
});

it('updates present fields', function () {
    $this->freezeTime(function ($now) {
        $now->setMicroseconds(0);
        $player = MinecraftPlayer::factory()->create();

        $this->withServerToken()
            ->patchJson("http://api.localhost/v3/players/$player->uuid", [
                'alias' => 'new_alias',
                'nickname' => 'new_nickname',
                'muted' => true,
                'walk_speed' => 10.5,
                'fly_speed' => 11.5,
            ])
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('alias', 'new_alias')
                ->where('nickname', 'new_nickname')
                ->where('muted', (int)true)
                ->where('walk_speed', 10.5)
                ->where('fly_speed', 11.5)
                ->where('updated_at', $now->toISOString())
                ->etc()
            );
    });
});

it('does not update missing fields', function () {
    $player = MinecraftPlayer::factory()->create([
        'walk_speed' => 1.5,
        'fly_speed' => 2.5,
    ]);
    $this->withServerToken()
        ->patchJson("http://api.localhost/v3/players/$player->uuid", [])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('alias', $player->alias)
            ->where('nickname', $player->nickname)
            ->where('muted', (int)$player->muted)
            ->where('walk_speed', $player->walk_speed)
            ->where('fly_speed', $player->fly_speed)
            ->where('updated_at', $player->updated_at->toISOString())
            ->etc()
        );
});
