<?php

namespace Repositories\GameIPBans;

use Entities\Models\Eloquent\GameIPBan;

final class GameIPBanMockRepository implements GameIPBanRepository
{
    public GameIPBan $create;
    public GameIPBan $find;

    public function create(
        int $bannerPlayerId,
        string $ip,
        string $reason,
    ): GameIPBan {
        return $this->create;
    }

    public function find(int $ip): ?GameIPBan
    {
        return $this->find;
    }
}
