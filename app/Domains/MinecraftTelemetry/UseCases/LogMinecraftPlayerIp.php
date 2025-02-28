<?php

namespace App\Domains\MinecraftTelemetry\UseCases;

use App\Models\MinecraftPlayerIp;

final class LogMinecraftPlayerIp
{
    public function execute(
        int $playerId,
        string $ip,
    ): void {
        // TODO: perform validation here later if needed
        // TODO: check for invalid sources like 127.0.0.1
        // TODO: check for known VPNs

        MinecraftPlayerIp::updateOrCreate(
            ['player_id' => $playerId, 'ip' => $ip],
            ['updated_at' => now()],
        );
    }
}
