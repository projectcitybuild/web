<?php

namespace Repositories\GameIPBans;

use Domain\Bans\UnbanType;
use Entities\Models\Eloquent\GameIPBan;

interface GameIPBanRepository
{
    public function create(
        string $ip,
        int $bannerPlayerId,
        string $reason,
    ): GameIPBan;

    public function find(string $ip): ?GameIPBan;

    public function firstActive(string $ip): ?GameIPBan;

    public function unban(
        GameIPBan $ban,
        int $unbannerPlayerId,
        UnbanType $unbanType,
    );
}
