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
            'world_uuid' => ['required_with:x,y,z,pitch,yaw', 'uuid'],
            'x' => ['required_with:world_uuid,y,z,pitch,yaw', 'numeric'],
            'y' => ['required_with:world_uuid,x,z,pitch,yaw', 'numeric'],
            'z' => ['required_with:world_uuid,x,y,pitch,yaw', 'numeric'],
            'pitch' => ['required_with:world_uuid,x,y,z,yaw', 'numeric'],
            'yaw' => ['required_with:world_uuid,x,y,z,pitch', 'numeric'],
        ]));

        $sessionTime = $validated->get('session_seconds');

        $uuid = MinecraftUUID::tryParse($validated->get('uuid'));
        $player = MinecraftPlayer::whereUuid($uuid)->firstOrFail();
        $player->last_seen_at = now();
        $player->play_time += $sessionTime;

        if ($validated->has('world_uuid')) {
            $player->last_location_world_uuid = $validated->get('world_uuid');
            $player->last_location_x = $validated->get('x');
            $player->last_location_y = $validated->get('y');
            $player->last_location_z = $validated->get('z');
            $player->last_location_pitch = $validated->get('pitch');
            $player->last_location_yaw = $validated->get('yaw');
        }

        // Prevent inflation from reconnect spam
        if ($sessionTime >= 20) {
            $player->sessions += 1;

            MinecraftPlayerSession::create([
                'player_id' => $player->id,
                'seconds' => $sessionTime,
                'starts_at' => now()->subSeconds($sessionTime),
                'ends_at' => now(),
            ]);
        }
        $player->save();

        return response()->json([]);
    }
}
