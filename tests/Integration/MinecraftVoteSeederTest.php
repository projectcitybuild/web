<?php

use App\Models\MinecraftPlayer;
use App\Models\Server;
use Database\Seeders\MinecraftVoteSeeder;

it('seeds frequent, sporadic, and non-voting player groups', function () {
    Server::factory()->create();
    MinecraftPlayer::factory()->count(10)->create();

    (new MinecraftVoteSeeder)->run();

    $voteCounts = MinecraftPlayer::query()
        ->withCount('votes')
        ->orderBy('id')
        ->pluck('votes_count')
        ->all();

    expect($voteCounts)->toHaveCount(10);

    foreach ([0, 1] as $index) {
        expect($voteCounts[$index])
            ->toBeGreaterThanOrEqual(20)
            ->toBeLessThanOrEqual(35);
    }

    foreach ([2, 3, 4] as $index) {
        expect($voteCounts[$index])
            ->toBeGreaterThanOrEqual(1)
            ->toBeLessThanOrEqual(6);
    }

    foreach ([5, 6, 7, 8, 9] as $index) {
        expect($voteCounts[$index])->toBe(0);
    }
});
