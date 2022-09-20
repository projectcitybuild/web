<?php

namespace Repositories\GameIPBans;

use Domain\Bans\UnbanType;
use Entities\Models\Eloquent\GameIPBan;

final class GameIPBanMockRepository implements GameIPBanRepository
{
    public GameIPBan $create;
    public ?GameIPBan $firstActive;

    public function create(
        string $ip,
        int $bannerPlayerId,
        string $reason,
    ): GameIPBan {
        return $this->create;
    }

    public function firstActive(string $ip): ?GameIPBan
    {
        return $this->firstActive;
    }

    public function unban(
        GameIPBan $ban,
        int $unbannerPlayerId,
        UnbanType $unbanType,
    ) {
        $ban->unbanned_at = now();
        $ban->unbanner_player_id = $unbannerPlayerId;
        $ban->unban_type = $unbanType;
    }
}
