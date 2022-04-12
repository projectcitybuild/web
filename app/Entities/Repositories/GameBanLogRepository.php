<?php

namespace App\Entities\Repositories;

use App\Entities\Models\Eloquent\GameBanLog;

final class GameBanLogRepository
{
    public function create(
        int $gameBanId,
        int $serverKeyId,
        int $banAction,
        ?string $ip
    ) {
        return GameBanLog::create([
            'game_ban_id' => $gameBanId,
            'server_key_id' => $serverKeyId,
            'ban_action' => $banAction,
            'incoming_ip' => $ip,
        ]);
    }
}
