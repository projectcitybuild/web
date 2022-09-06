<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\APIController;
use Domain\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayerUseCase;
use Illuminate\Http\Request;

final class MinecraftTelemetryController extends APIController
{
    public function playerSeen(
        Request $request,
        UpdateSeenMinecraftPlayerUseCase $updateSeenMinecraftPlayer,
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
