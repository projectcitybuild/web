<?php

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerNicknameChange;

it('stores nickname changes with their player and change time', function () {
    $player = MinecraftPlayer::factory()->create();
    $changedAt = now()->subDay();

    $nicknameChange = MinecraftPlayerNicknameChange::factory()->create([
        'player_id' => $player->id,
        'new_nickname' => 'CityBuilder',
        'changed_at' => $changedAt,
    ]);

    expect($nicknameChange->player->is($player))->toBeTrue();
    expect($player->nicknameChanges()->first()?->is($nicknameChange))->toBeTrue();
    expect($nicknameChange->changed_at->toDateTimeString())->toBe($changedAt->toDateTimeString());
});
