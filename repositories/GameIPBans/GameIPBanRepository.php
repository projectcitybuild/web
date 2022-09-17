<?php

namespace Repositories\GameIPBans;

use Entities\Models\Eloquent\GameIPBan;

interface GameIPBanRepository
{
    public function create(
        string $ip,
        int $bannerPlayerId,
        string $reason,
    ): GameIPBan;

    public function find(int $ip): ?GameIPBan;
}
