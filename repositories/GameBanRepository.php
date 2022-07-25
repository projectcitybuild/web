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
        bool $skipTempBans,
    ): ?GameBan {
        return GameBan::where('banned_player_id', $player->getKey())
            ->active()
            ->when($skipTempBans, function ($q) {
                $q->whereNull('expires_at');
            })
            ->first();
    }

    public function deactivateAllTemporaryBans(MinecraftPlayer $player) {
        GameBan::where('banned_player_id', $player->getKey())
            ->active()
            ->whereNotNull('expires_at')
            ->update(['is_active' => false]);
    }
}
