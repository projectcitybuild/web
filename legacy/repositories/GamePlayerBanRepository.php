<?php

namespace Repositories;

use Domain\Bans\UnbanType;
use Entities\Models\Eloquent\GamePlayerBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class GamePlayerBanRepository
{
    public function create(
        int $serverId,
        int $bannedPlayerId,
        string $bannedPlayerAlias,
        int $bannerPlayerId,
        ?string $reason,
        ?Carbon $expiresAt,
    ): GamePlayerBan {
        return GamePlayerBan::create([
            'server_id' => $serverId,
            'banned_player_id' => $bannedPlayerId,
            'banned_alias_at_time' => $bannedPlayerAlias,
            'banner_player_id' => $bannerPlayerId,
            'reason' => $reason,
            'expires_at' => $expiresAt,
        ]);
    }

    public function find(int $banId): ?GamePlayerBan
    {
        return GamePlayerBan::find($banId);
    }

    public function firstActiveBan(MinecraftPlayer $player): ?GamePlayerBan
    {
        return GamePlayerBan::where('banned_player_id', $player->getKey())
            ->active()
            ->first();
    }

    public function all(MinecraftPlayer $player): Collection
    {
        return GamePlayerBan::where('banned_player_id', $player->getKey())
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->get();
    }

    public function unbanAllExpired()
    {
        GamePlayerBan::whereNull('unbanned_at')
            ->whereDate('expires_at', '<=', now())
            ->update([
                'unbanned_at' => now(),
                'unban_type' => UnbanType::EXPIRED->value,
            ]);
    }

    public function unban(
        GamePlayerBan $ban,
        ?int $unbannerPlayerId,
        UnbanType $unbanType,
    ) {
        $ban->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayerId,
            'unban_type' => $unbanType->value,
        ]);
    }
}
