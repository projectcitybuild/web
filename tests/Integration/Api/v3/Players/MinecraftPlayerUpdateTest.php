<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\Account;
use App\Models\Badge;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\GameIPBan;
use App\Models\GamePlayerBan;
use App\Models\Group;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Carbon;
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
                'nickname' => 'new_nickname'
            ])
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('alias', 'new_alias')
                ->where('nickname', 'new_nickname')
                ->where('updated_at', $now->toISOString())
                ->etc()
            );
    });
});

it('does not update missing fields', function () {
    $player = MinecraftPlayer::factory()->create();

    $this->withServerToken()
        ->patchJson("http://api.localhost/v3/players/$player->uuid", [])
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('alias', $player->alias)
            ->where('nickname', $player->nickname)
            ->where('updated_at', $player->updated_at->toISOString())
            ->etc()
        );
});
