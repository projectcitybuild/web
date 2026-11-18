<?php

use App\Models\MinecraftBuild;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftWarp;

it('returns build items with their existing fields and paginator envelope', function () {
    $player = MinecraftPlayer::factory()->create();
    $build = MinecraftBuild::factory()->create([
        'player_id' => $player->id,
        'name' => 'contract-build',
    ]);

    $this->getJson('http://api.localhost/v3/builds?page_size=1')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [[
                'id', 'player_id', 'name', 'description', 'lore', 'votes', 'world',
                'x', 'y', 'z', 'pitch', 'yaw', 'created_at', 'updated_at',
            ]],
            'links',
            'current_page',
            'total',
        ])
        ->assertJsonPath('data.0.id', $build->id)
        ->assertJsonPath('data.0.name', $build->name);
});

it('returns warp items with their existing fields and paginator envelope', function () {
    $warp = MinecraftWarp::factory()->create(['name' => 'contract-warp']);

    $this->getJson('http://api.localhost/v3/warps?page_size=1')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [[
                'id', 'name', 'world', 'x', 'y', 'z', 'pitch', 'yaw',
                'created_at', 'updated_at',
            ]],
            'links',
            'current_page',
            'total',
        ])
        ->assertJsonPath('data.0.id', $warp->id)
        ->assertJsonPath('data.0.name', $warp->name)
        ->assertJsonPath('data.0.pitch', (string) $warp->pitch)
        ->assertJsonPath('data.0.created_at', $warp->created_at->toISOString());
});

it('returns a build with its loaded player fields', function () {
    $player = MinecraftPlayer::factory()->create();
    $build = MinecraftBuild::factory()->create(['player_id' => $player->id]);

    $this->getJson("http://api.localhost/v3/builds/{$build->id}")
        ->assertOk()
        ->assertJsonPath('id', $build->id)
        ->assertJsonPath('player.id', $player->id)
        ->assertJsonPath('player.uuid', $player->uuid)
        ->assertJsonStructure([
            'id', 'player_id', 'name', 'description', 'lore', 'votes', 'world',
            'x', 'y', 'z', 'pitch', 'yaw', 'created_at', 'updated_at',
            'player' => ['id', 'uuid', 'alias', 'sessions', 'created_at', 'updated_at'],
        ]);
});

it('returns all warps as an unpaginated list', function () {
    $warp = MinecraftWarp::factory()->create(['name' => 'contract-warp']);

    $this->getJson('http://api.localhost/v3/warps/all')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $warp->id)
        ->assertJsonPath('0.name', $warp->name)
        ->assertJsonPath('0.created_at', $warp->created_at->toISOString());
});

it('creates a warp with the resource response status', function () {
    $this->postJson('http://api.localhost/v3/warps', [
        'name' => 'created-warp',
        'world' => 'world',
        'x' => 1,
        'y' => 64,
        'z' => 2,
        'pitch' => 0,
        'yaw' => 90,
    ])
        ->assertCreated()
        ->assertJsonPath('name', 'created-warp')
        ->assertJsonPath('world', 'world');
});

it('returns build, warp, and home names with only their identifiers', function () {
    $player = MinecraftPlayer::factory()->create();
    $build = MinecraftBuild::factory()->create([
        'player_id' => $player->id,
        'name' => 'contract-build',
    ]);
    $warp = MinecraftWarp::factory()->create(['name' => 'contract-warp']);
    $home = MinecraftHome::factory()->create([
        'player_id' => $player->id,
        'name' => 'contract-home',
    ]);

    $this->getJson('http://api.localhost/v3/builds/names')
        ->assertOk()
        ->assertExactJson([['id' => $build->id, 'name' => $build->name]]);

    $this->getJson('http://api.localhost/v3/warps/names')
        ->assertOk()
        ->assertExactJson([['id' => $warp->id, 'name' => $warp->name]]);

    $this->getJson("http://api.localhost/v3/players/{$player->uuid}/homes/names")
        ->assertOk()
        ->assertExactJson([['id' => $home->id, 'name' => $home->name]]);
});
