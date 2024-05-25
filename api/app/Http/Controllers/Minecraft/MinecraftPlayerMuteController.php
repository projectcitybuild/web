<?php

namespace App\Http\Controllers\Minecraft;

use App\Actions\GetOrCreatePlayer;
use App\Http\Controllers\Controller;
use App\Models\Eloquent\Player;
use App\Models\Eloquent\PlayerMute;
use App\Models\MinecraftUUID;
use App\Models\Rules\MinecraftUUIDRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class MinecraftPlayerMuteController extends Controller
{
    public function show(MinecraftUUID $uuid): JsonResponse
    {
        $player = Player::uuid($uuid)->first();
        if ($player === null) {
            return response()->json(null);
        }
        $mute = PlayerMute::forPlayer($player)->firstOrFail();
        return response()->json($mute);
    }

    public function store(
        Request $request,
        MinecraftUUID $uuid,
        GetOrCreatePlayer $getOrCreatePlayer,
    ): JsonResponse {
        $validated = $request->validate([
           'muter_uuid' => [new MinecraftUUIDRule],
        ]);

        $mutedPlayer = $getOrCreatePlayer($uuid);

        $muterPlayer = null;
        if (! empty($validated['muter_uuid'])) {
            $muterUuid = new MinecraftUUID($validated['muter_uuid']);
            $muterPlayer = $getOrCreatePlayer($muterUuid);
        }

        if (PlayerMute::forPlayer($mutedPlayer)->exists()) {
            throw ValidationException::withMessages([
                'uuid' => ['This UUID is already muted'],
            ]);
        }
        $mute = PlayerMute::create([
            'muted_player_id' => $mutedPlayer->getKey(),
            'muter_player_id' => $muterPlayer?->getKey(),
        ]);

        return response()->json($mute);
    }

    public function delete(
        Request $request,
        MinecraftUUID $uuid,
        GetOrCreatePlayer $getOrCreatePlayer,
    ): JsonResponse {
        $mutedPlayer = $getOrCreatePlayer($uuid);
        $mute = PlayerMute::forPlayer($mutedPlayer)->firstOrFail();
        $mute->delete();

        return response()->json($mute);
    }
}
