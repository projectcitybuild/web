<?php

namespace Domain\Bans\Repositories;

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
        GamePlayerType $bannedPlayerType,
        string $bannedPlayerAlias,
        int $bannerPlayerId,
        GamePlayerType $bannerPlayerType,
        bool $isGlobalBan,
        ?string $reason,
        ?Carbon $expiresAt,
    ): GameBan {
        return GameBan::create([
            'server_id' => $serverId,
            'banned_player_id' => $bannedPlayerId,
            'banned_player_type' => $bannedPlayerType->value,
            'banned_alias_at_time' => $bannedPlayerAlias,
            'staff_player_id' => $bannerPlayerId,
            'staff_player_type' => $bannerPlayerType->value,
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
            ->where('banned_player_type', $identifier->gameIdentifierType->playerType()->value)
            ->active()
            ->first();
    }
}
