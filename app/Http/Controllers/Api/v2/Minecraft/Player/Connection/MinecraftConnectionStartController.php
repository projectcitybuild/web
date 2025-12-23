<?php

namespace App\Http\Controllers\Api\v2\Minecraft\Player\Connection;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Rules\MinecraftUUIDRule;
use App\Domains\MinecraftTelemetry\UseCases\LogMinecraftPlayerIp;
use App\Domains\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftConnectionStartController extends ApiController
{
    public function __construct(
        private readonly UpdateSeenMinecraftPlayer $updateSeenMinecraftPlayer,
        private readonly LogMinecraftPlayerIp $logMinecraftPlayerIp,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uuid' => ['required', new MinecraftUUIDRule],
            'alias' => ['required', 'string'],
            'ip' => 'nullable',
        ]);

        $player = $this->updateSeenMinecraftPlayer->execute(
            uuid: new MinecraftUUID($validated['uuid']),
            alias: $validated['alias'],
        );

        $ip = $validated['ip'] ?? '';
        if (! empty($ip)) {
            $this->logMinecraftPlayerIp->execute(
                playerId: $player->getKey(),
                ip: $ip,
            );
        }

        return response()->json();
    }
}
