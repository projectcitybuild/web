<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftConfig;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires server token', function () {
    $uuid = MinecraftUUID::random();

    $this->getJson('http://api.localhost/v3/server/config')
        ->assertUnauthorized();

    $status = $this
        ->withServerToken()
        ->getJson('http://api.localhost/v3/server/config')
        ->status();

    expect($status)->not->toEqual(401);
});

it('returns latest config', function () {
    for ($i = 1; $i <= 10; $i++) {
        MinecraftConfig::factory()->count(10)->create([
            'version' => $i,
        ]);
    }
    $latest = MinecraftConfig::factory()->create([
        'version' => ++$i,
    ]);

    $this->withServerToken()
        ->getJson('http://api.localhost/v3/server/config')
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('id', $latest->id)
            ->where('version', $latest->version)
            ->where('config', $latest->config)
            ->etc()
        );
});
