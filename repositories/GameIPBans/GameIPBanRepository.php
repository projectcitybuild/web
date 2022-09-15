<?php

namespace Repositories\GameIPBans;

use Entities\Models\Eloquent\GameIPBan;

interface GameIPBanRepository
{
    public function create(
        int $warnerPlayerId,
        string $ip,
        string $reason,
    ): GameIPBan;

    public function find(int $ip): ?GameIPBan;
}
