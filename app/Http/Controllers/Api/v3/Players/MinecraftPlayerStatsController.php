<?php

namespace App\Http\Controllers\Api\v3\Players;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

class MinecraftPlayerStatsController
{
    public function store(Request $request, MinecraftUUID $uuid)
    {
        $validated = collect($request->validate([
            'afk_time' => ['sometimes', 'integer', 'gt:0'],
            'blocks_placed' => ['sometimes', 'integer', 'gt:0'],
            'blocks_destroyed' => ['sometimes', 'integer', 'gt:0'],
            'blocks_travelled' => ['sometimes', 'integer', 'gt:0'],
        ]));

        $player = MinecraftPlayer::whereUuid($uuid)->firstOrFail();

        $updated = [];
        if ($validated->has('afk_time')) {
            $updated['afk_time'] = $player->afk_time + $validated->get('afk_time');
        }
        if ($validated->has('blocks_placed')) {
            $updated['blocks_placed'] = $player->blocks_placed + $validated->get('blocks_placed');
        }
        if ($validated->has('blocks_destroyed')) {
            $updated['blocks_destroyed'] = $player->blocks_destroyed + $validated->get('blocks_destroyed');
        }
        if ($validated->has('blocks_travelled')) {
            $updated['blocks_travelled'] = $player->blocks_travelled + $validated->get('blocks_travelled');
        }

        if (! empty($updated)) {
            $player->update($updated);
        }
        return $player->refresh();
    }
}
