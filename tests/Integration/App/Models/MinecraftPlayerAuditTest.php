<?php

use App\Models\Account;
use App\Models\MinecraftPlayer;

beforeEach(function () {
    config(['audit.console' => true]);
});

it('does not audit routine Minecraft statistics', function () {
    $player = MinecraftPlayer::factory()->create();
    $auditCount = $player->audits()->count();

    $player->update([
        'play_time' => 100,
        'afk_time' => 20,
        'blocks_placed' => 12,
        'blocks_destroyed' => 8,
        'blocks_travelled' => 500,
        'last_seen_at' => now(),
    ]);

    expect($player->audits()->count())->toBe($auditCount);
});

it('audits account linking without recording accompanying statistics', function () {
    $player = MinecraftPlayer::factory()->create(['account_id' => null]);
    $account = Account::factory()->create();

    $player->update([
        'account_id' => $account->id,
        'play_time' => 100,
    ]);

    $audit = $player->audits()->where('event', 'updated')->firstOrFail();

    expect($audit->new_values)->toBe(['account_id' => $account->id]);
});
