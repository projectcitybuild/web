<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

function putBan(int $id, array $data)
{
    return test()->withServerToken()->put("api/v2/bans/{$id}", $data);
}

describe('validation', function () {
    it('fails when fields missing', function () {
        putBan(0, [])->assertInvalid([
            'banned_uuid',
            'banned_alias',
            'reason',
            'created_at',
        ]);
    });

    it('fails when invalid UUID is given', function () {
        putBan(0, [
            'banned_uuid' => 'not-a-uuid',
            'banned_alias' => fake()->userName(),
            'reason' => fake()->text(),
        ])->assertInvalid(['banned_uuid']);
    });

    it('fails when expires_at is not a date', function () {
        putBan(0, [
            'banned_uuid' => fake()->uuid(),
            'banned_alias' => fake()->userName(),
            'reason' => fake()->text(),
            'expires_at' => 'not-a-date',
        ])->assertInvalid(['expires_at']);
    });

    it('fails when created_at is not a date', function () {
        putBan(0, [
            'banned_uuid' => fake()->uuid(),
            'banned_alias' => fake()->userName(),
            'reason' => fake()->text(),
            'created_at' => 'not-a-date',
        ])->assertInvalid(['created_at']);
    });

    it('fails when an invalid unban_type is given', function () {
        putBan(0, [
            'banned_uuid' => fake()->uuid(),
            'banned_alias' => fake()->userName(),
            'reason' => fake()->text(),
            'unban_type' => 'not-a-date',
        ])->assertInvalid(['unban_type']);
    });
});

it('fails if ban not found', function () {
    putBan(123, [
        'banned_uuid'  => MinecraftUUID::random(),
        'banned_alias' => fake()->userName(),
        'banner_uuid'  => MinecraftUUID::random(),
        'banner_alias' => fake()->userName(),
        'reason'       => 'test',
        'created_at'   => now(),
    ])->assertNotFound();
});

it('updates an existing ban', function () {
    $newBanned = MinecraftPlayer::factory()->create();
    $newBanner = MinecraftPlayer::factory()->create();
    $newUnbanner = MinecraftPlayer::factory()->create();
    $newCreatedAt = now()->subDay()->toISOString();

    $ban = GamePlayerBan::factory()
        ->bannedPlayer(MinecraftPlayer::factory())
        ->bannedBy(MinecraftPlayer::factory())
        ->create();

    putBan($ban->getKey(), [
        'banned_uuid' => $newBanned->uuid,
        'banned_alias' => $newBanned->alias,
        'banner_uuid' => $newBanner->uuid,
        'banner_alias' => $newBanner->alias,
        'reason' => 'updated',
        'created_at' => $newCreatedAt,
        'unbanned_at' => now(),
        'unbanner_uuid' => $newUnbanner->uuid,
        'unbanner_alias' => $newUnbanner->alias,
    ])->assertSuccessful();

    $this->assertDatabaseHas(GamePlayerBan::tableName(), [
        'id' => $ban->getKey(),
        'reason' => 'updated',
        'banned_player_id' => $newBanned->getKey(),
        'banner_player_id' => $newBanner->getKey(),
        'unbanner_player_id' => $newUnbanner->getKey(),
    ]);
});
