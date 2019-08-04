<?php

namespace App\Http\Actions\GameBans;

use App\Entities\Bans\Models\GameBan;
use App\Entities\GamePlayerType;

final class GetPlayerBans
{
    public function execute(bool $firstOnly, int $bannedPlayerId, GamePlayerType $bannedPlayerType, int $serverId = null, array $with = []) {
        $bans = GameBan::with($with)
            ->where('banned_player_id', $bannedPlayerId)
            ->where('banned_player_type', $bannedPlayerType->valueOf())
            ->where('is_active', true)
            ->when(isset($serverId),
                function ($q) use ($serverId) {
                    return $q->where('server_id', $serverId)
                             ->orWhere('is_global_ban', true);
                },
                function ($q) {
                    return $q->where('is_global_ban', true);
                }
            );

        if ($firstOnly) {
            return $bans->first();
        } else {
            return $bans->get();
        }
    }
}