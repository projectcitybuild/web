<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftTelemetryController extends ApiController
{
    public function playerSeen(
        Request $request,
        UpdateSeenMinecraftPlayer $updateSeenMinecraftPlayer,
    ) {
        $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['required', 'string'],
        ]);

        $updateSeenMinecraftPlayer->execute(
            uuid: new MinecraftUUID($request->get('uuid')),
            alias: $request->get('alias'),
        );

        return response()->json(null);
    }
}
