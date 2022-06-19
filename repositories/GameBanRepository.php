<?php

namespace Repositories;

use Entities\Models\Eloquent\GameBan;
use Entities\Models\GamePlayerType;
use Illuminate\Support\Carbon;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

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
        bool $isGlobalBan,
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
            'is_global_ban' => $isGlobalBan,
            'expires_at' => $expiresAt,
        ]);
    }

    public function firstActiveBan(
        PlayerIdentifier $identifier,
    ): ?GameBan {
        return GameBan::where('banned_player_id', $identifier->key)
            ->active()
            ->first();
    }
}
