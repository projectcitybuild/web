<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\MinecraftStats\Data\PlayerStatIncrement;
use App\Domains\MinecraftStats\Jobs\IncrementPlayerStatsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MinecraftStatsController
{
    public function store(Request $request)
    {
        $validated = collect($request->validate([
            'players' => ['required', 'array'],
            'players.*.afk_time' => ['sometimes', 'integer', 'gt:0'],
            'players.*.blocks_placed' => ['sometimes', 'integer', 'gte:0'],
            'players.*.blocks_destroyed' => ['sometimes', 'integer', 'gte:0'],
            'players.*.blocks_travelled' => ['sometimes', 'integer', 'gte:0'],
        ]));

        // Nested array key validation is too finicky to insert above
        $uuids = $validated->get('players');
        $uuidValidator = Validator::make(array_keys($uuids), ['*' => new MinecraftUUIDRule]);
        $uuidValidator->validate();

        foreach ($uuids as $uuid => $stats) {
            $increment = new PlayerStatIncrement(
                afkTime: $stats['afk_time'] ?? 0,
                blocksPlaced: $stats['blocks_placed'] ?? 0,
                blocksDestroyed: $stats['blocks_destroyed'] ?? 0,
                blocksTravelled: $stats['blocks_travelled'] ?? 0,
            );
            if ($increment->isNonZero()) {
                IncrementPlayerStatsJob::dispatch(new MinecraftUUID($uuid), $increment);
            }
        }
    }
}
