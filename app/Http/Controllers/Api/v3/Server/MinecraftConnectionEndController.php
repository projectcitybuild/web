<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;
use Illuminate\Http\Request;

final class MinecraftConnectionEndController extends ApiController
{
    public function __invoke(Request $request)
    {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'session_seconds' => ['required', 'integer', 'gte:0'],
        ]));

        $sessionTime = $validated->get('session_seconds');

        $uuid = MinecraftUUID::tryParse($validated->get('uuid'));
        $player = MinecraftPlayer::whereUuid($uuid)->firstOrFail();
        $player->last_seen_at = now();
        $player->play_time += $sessionTime;

        // Prevent inflation from reconnect spam
        if ($sessionTime >= 20) {
            $player->sessions += 1;

            MinecraftPlayerSession::create([
                'player_id' => $player->getKey(),
                'seconds' => $sessionTime,
                'starts_at' => now()->subSeconds($sessionTime),
                'ends_at' => now(),
            ]);
        }
        $player->save();

        return response()->json();
    }
}
