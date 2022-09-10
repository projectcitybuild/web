<?php

namespace Repositories;

use Domain\Bans\UnbanType;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

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
            ->active()
            ->first();
    }

    public function all(MinecraftPlayer $player): Collection
    {
        return GameBan::where('banned_player_id', $player->getKey())
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->get();
    }

    public function unbanAllExpired()
    {
        GameBan::whereNull('unbanned_at')
            ->whereDate('expires_at', '<=', now())
            ->update([
                'unbanned_at' => now(),
                'unban_type' => UnbanType::EXPIRED,
            ]);
    }

    public function unban(
        GameBan $ban,
        ?int $unbannerPlayerId,
        UnbanType $unbanType,
    ) {
        $ban->save([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayerId,
            'unban_type' => $unbanType->value,
        ]);
    }
}
