<?php

namespace Repositories\GameIPBans;

use Domain\Bans\UnbanType;
use Entities\Models\Eloquent\GameIPBan;

final class GameIPBanEloquentRepository implements GameIPBanRepository
{
    public function create(
        string $ip,
        int $bannerPlayerId,
        string $reason,
    ): GameIPBan {
        return GameIPBan::create([
            'banner_player_id' => $bannerPlayerId,
            'ip_address' => $ip,
            'reason' => $reason,
        ]);
    }

    public function find(string $ip): ?GameIPBan
    {
        return GameIPBan::where('ip_address', $ip)->first();
    }

    public function firstActive(string $ip): ?GameIPBan
    {
        return GameIPBan::where('ip_address', $ip)
            ->whereNull('unbanned_at')
            ->first();
    }

    public function unban(
        GameIPBan $ban,
        int $unbannerPlayerId,
        UnbanType $unbanType,
    ) {
        $ban->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayerId,
            'unban_type' => $unbanType->value,
        ]);
    }
}
