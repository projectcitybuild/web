<?php

namespace Repositories\GameIPBans;

use Entities\Models\Eloquent\GameIPBan;

final class GameIPBanEloquentRepository implements GameIPBanRepository
{
    public function create(
        int $bannerPlayerId,
        string $ip,
        string $reason,
    ): GameIPBan {
        return GameIPBan::create([
            'banner_player_id' => $bannerPlayerId,
            'ip_address' => $ip,
            'reason' => $reason,
        ]);
    }

    public function find(int $ip): ?GameIPBan
    {
        return GameIPBan::where('ip_address', $ip)->first();
    }
}
