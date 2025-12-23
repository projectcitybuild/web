<?php

namespace Tests\Integration\Api\v2\Bans\DeletePlayerBanTest;

use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Event;

function putBan(int $id, array $data)
{
    return test()->withServerToken()->put("api/v2/bans/uuid/{$id}", $data);
}

beforeEach(function () {
    Event::fake();
});

it('deletes an existing ban', function () {
    $banned = MinecraftPlayer::factory()->create();
    $banner = MinecraftPlayer::factory()->create();

    $ban = GamePlayerBan::factory()
        ->bannedPlayer($banned)
        ->bannedBy($banner)
        ->create();

    putBan($ban->id, [
        'banned_uuid' => $banned->uuid,
        'banned_alias' => $banned->alias,
        'banner_uuid' => $banner->uuid,
        'banner_alias' => $banner->alias,
        'reason' => 'Updated reason',
        'created_at' => now()->subDay()->toISOString(),
    ])->assertSuccessful();

    $this->assertDatabaseHas(GamePlayerBan::tableName(), [
        'id' => $ban->id,
        'reason' => 'Updated reason',
    ]);
});
