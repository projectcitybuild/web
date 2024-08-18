<?php

namespace App\Http\Controllers\Api\v1;

use App\Domains\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftTelemetryController extends ApiController
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
