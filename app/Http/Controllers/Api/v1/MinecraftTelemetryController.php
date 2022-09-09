<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\ApiController;
use Domain\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayer;
use Illuminate\Http\Request;

final class MinecraftTelemetryController extends ApiController
{
    public function playerSeen(
        Request                   $request,
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
