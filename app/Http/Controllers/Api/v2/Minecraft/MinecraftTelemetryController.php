<?php

namespace App\Http\Controllers\Api\v2\Minecraft;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\MinecraftTelemetry\UseCases\LogMinecraftPlayerIp;
use App\Domains\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftTelemetryController extends ApiController
{
    public function playerSeen(
        Request $request,
        UpdateSeenMinecraftPlayer $updateSeenMinecraftPlayer,
        LogMinecraftPlayerIp $logMinecraftPlayerIp,
    ) {
        $validated = $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['required', 'string'],
            'ip' => 'nullable',
        ]);

        $player = $updateSeenMinecraftPlayer->execute(
            uuid: new MinecraftUUID($validated['uuid']),
            alias: $validated['alias'],
        );

        $ip = $validated['ip'] ?? '';
        if (!empty($ip)) {
            $logMinecraftPlayerIp->execute(
                playerId: $player->getKey(),
                ip: $ip,
            );
        }

        return response()->json(null);
    }
}
