<?php

use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Event;

beforeAll(function () {
    $this->putBan = function (array $data) {
        return test()->withServerToken()->post('api/v2/ban', $data);
    };
});

beforeEach(function () {
    Event::fake();
});

describe('validation', function () {
    it('fails when fields missing', function () {
        $this->postBan([])->assertInvalid([
            'banned_uuid',
            'banned_alias',
            'reason',
        ]);
    });

    it('fails when invalid UUID is given', function () {
        $this->postBan([
            'banned_uuid' => 'not-a-uuid',
            'banned_alias' => fake()->userName(),
            'reason' => fake()->text(),
        ])->assertInvalid(['banned_uuid']);
    });

    it('fails when expires_at is not a date', function () {
        $this->postBan([
            'banned_uuid' => fake()->uuid(),
            'banned_alias' => fake()->userName(),
            'reason' => fake()->text(),
            'expires_at' => 'not-a-date',
        ])->assertInvalid(['expires_at']);
    });

    it('fails when created_at is not a date', function () {
        $this->postBan([
            'banned_uuid' => fake()->uuid(),
            'banned_alias' => fake()->userName(),
            'reason' => fake()->text(),
            'created_at' => 'not-a-date',
        ])->assertInvalid(['created_at']);
    });

    it('fails when an invalid unban_type is given', function () {
        $this->postBan([
            'banned_uuid' => fake()->uuid(),
            'banned_alias' => fake()->userName(),
            'reason' => fake()->text(),
            'unban_type' => 'not-a-date',
        ])->assertInvalid(['unban_type']);
    });
});

it('returns conflict when player is already banned', function () {
    $player = MinecraftPlayer::factory()->create();
    GamePlayerBan::factory()->bannedPlayer($player)->create();

    $this->postBan([
        'banned_uuid' => $player->uuid,
        'banned_alias' => $player->alias,
        'reason' => fake()->text(),
    ])->assertConflict();
});

it('creates ban with existing players', function () {
    $banned = MinecraftPlayer::factory()->create();
    $banner = MinecraftPlayer::factory()->create();

    $this->ostBan([
        'banned_uuid'  => $banned->uuid,
        'banned_alias' => $banned->alias,
        'banner_uuid'  => $banner->uuid,
        'banner_alias' => $banner->alias,
        'reason'       => 'test',
    ])->assertSuccessful();

    $this->assertDatabaseHas(GamePlayerBan::tableName(), [
        'banned_player_id' => $banned->getKey(),
        'banner_player_id' => $banner->getKey(),
        'reason' => 'test',
    ]);
});

it('allows anonymous bans when banner is null', function () {
    $banned = MinecraftPlayer::factory()->create();

    $this->postBan([
        'banned_uuid' => $banned->uuid,
        'banned_alias' => $banned->alias,
        'reason' => fake()->text(),
    ])->assertSuccessful();

    $this->assertDatabaseHas(GamePlayerBan::tableName(), [
        'banned_player_id' => $banned->getKey(),
        'banner_player_id' => null,
    ]);
});

it('creates the banned player when they do not exist', function () {
    $banned = MinecraftPlayer::factory()->make();

    $this->postBan([
        'banned_uuid'  => $banned->uuid,
        'banned_alias' => $banned->alias,
        'reason'       => fake()->text(),
    ])->assertSuccessful();

    $this->assertDatabaseHas(MinecraftPlayer::tableName(), [
        'uuid' => $banned->uuid,
        'alias' => $banned->alias,
    ]);
});

it('creates the banner player when they do not exist', function () {
    $banned = MinecraftPlayer::factory()->create();
    $banner = MinecraftPlayer::factory()->make();

    $this->postBan([
        'banned_uuid'  => $banned->uuid,
        'banned_alias' => $banned->alias,
        'banner_uuid'  => $banner->uuid,
        'banner_alias' => $banner->alias,
        'reason'       => fake()->text(),
    ])->assertSuccessful();

    $this->assertDatabaseHas(MinecraftPlayer::tableName(), [
        'uuid' => $banner->uuid,
        'alias' => $banner->alias,
    ]);
});
