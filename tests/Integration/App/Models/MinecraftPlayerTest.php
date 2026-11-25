<?php

use App\Models\MinecraftPlayer;

it('persists combat statistics on the player', function () {
    $player = MinecraftPlayer::factory()->create([
        'deaths' => 12,
        'mob_kills' => 34,
        'player_kills' => 5,
    ]);

    expect($player->only(['deaths', 'mob_kills', 'player_kills']))->toBe([
        'deaths' => 12,
        'mob_kills' => 34,
        'player_kills' => 5,
    ]);
});
