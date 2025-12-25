<?php

namespace App\Http\Controllers\Api\v3\Server;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Http\Controllers\ApiController;
use App\Models\MinecraftPlayer;
use Illuminate\Http\Request;

final class MinecraftConnectionEndController extends ApiController
{
    public function __invoke(Request $request)
    {
        $validated = collect($request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
        ]));

        $uuid = MinecraftUUID::tryParse($validated->get('uuid'));
        $player = MinecraftPlayer::whereUuid($uuid)->firstOrFail();
        $player->last_seen_at = now();
        $player->save();

        return response()->json();
    }
}
