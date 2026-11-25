<?php

use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerKill;

it('relates a kill to its attacker and victim', function () {
    $attacker = MinecraftPlayer::factory()->create();
    $victim = MinecraftPlayer::factory()->create();
    $killedAt = now()->subMinutes(5);

    $kill = MinecraftPlayerKill::factory()->create([
        'player_id' => $attacker->id,
        'victim_player_id' => $victim->id,
        'weapon' => 'diamond_sword',
        'killed_at' => $killedAt,
    ]);

    expect($kill->player->is($attacker))->toBeTrue();
    expect($kill->victim->is($victim))->toBeTrue();
    expect($attacker->killsAsAttacker()->first()?->is($kill))->toBeTrue();
    expect($victim->killsAsVictim()->first()?->is($kill))->toBeTrue();
    expect($kill->killed_at->toDateTimeString())->toBe($killedAt->toDateTimeString());
});
