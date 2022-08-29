<?php

namespace Repositories;

use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Eloquent\Collection;
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

    public function find(int $banId): ?GameBan
    {
        return GameBan::find($banId);
    }

    public function firstActiveBan(MinecraftPlayer $player): ?GameBan
    {
        return GameBan::where('banned_player_id', $player->getKey())
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhereDate('expires_at', '>=', now());
            })
            ->first();
    }

    public function all(MinecraftPlayer $player): Collection
    {
        return GameBan::where('banned_player_id', $player->getKey())
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->get();
    }

    public function deactivateActiveExpired()
    {
        GameBan::where('is_active', true)
            ->where('expired_at', '<=', now())
            ->save(['is_active', false]);
    }
}
