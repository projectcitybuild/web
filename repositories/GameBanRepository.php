<?php

namespace Repositories;

use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Support\Carbon;

/**
 * @final
 */
class GameBanRepository
{
    public function create(
        int $serverId,
        int $bannedPlayerId,
        string $bannedPlayerAlias,
        int $bannerPlayerId,
        ?string $reason,
        ?Carbon $expiresAt,
    ): GameBan {
        return GameBan::create([
            'server_id' => $serverId,
            'banned_player_id' => $bannedPlayerId,
            'banned_alias_at_time' => $bannedPlayerAlias,
            'staff_player_id' => $bannerPlayerId,
            'reason' => $reason,
            'is_active' => true,
            'is_global_ban' => true,
            'expires_at' => $expiresAt,
        ]);
    }

    public function firstActiveBan(
        MinecraftPlayer $player,
    ): ?GameBan {
        return GameBan::where('banned_player_id', $player->getKey())
            ->active()
            ->first();
    }
}
