<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\APIController;
use Domain\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayer;
use Illuminate\Http\Request;

final class MinecraftTelemetryController extends APIController
{
    public function playerSeen(
        Request $request,
        UpdateSeenMinecraftPlayer $updateSeenMinecraftPlayer,
    ) {
        $this->validateRequest($request->all(), [
            'uuid' => 'required|string',
            'alias' => 'required|string',
        ]);

        $updateSeenMinecraftPlayer->execute(
            uuid: $request->get('uuid'),
            alias: $request->get('alias'),
        );

        return response()->json(null);
    }
}
