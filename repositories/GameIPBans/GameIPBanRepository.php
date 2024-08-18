<?php

namespace Repositories\GameIPBans;

use App\Models\GameIPBan;
use Domain\Bans\UnbanType;

interface GameIPBanRepository
{
    public function create(
        string $ip,
        int $bannerPlayerId,
        string $reason,
    ): GameIPBan;

    public function firstActive(string $ip): ?GameIPBan;

    public function unban(
        GameIPBan $ban,
        int $unbannerPlayerId,
        UnbanType $unbanType,
    );
}
