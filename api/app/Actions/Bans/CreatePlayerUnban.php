<?php

namespace App\Actions\Bans;

use App\Actions\GetOrCreatePlayer;
use App\Models\Eloquent\Player;
use App\Models\Eloquent\PlayerBan;
use App\Models\Events\PlayerUnbannedEvent;
use App\Models\UnbanType;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreatePlayerUnban
{
    /**
     * @throws ModelNotFoundException if $bannedPlayerUUID is not currently banned
     */
    public function create(
        String $bannedPlayerUUID,
        ?String $unbannerPlayerUUID,
        ?String $reason = null,
    ): PlayerBan {
        $bannedPlayer = Player::uuid($bannedPlayerUUID)->firstOrFail();
        $activeBan = PlayerBan::forPlayer($bannedPlayer)->active()->firstOrFail();

        $unbannerPlayer = (! empty($unbannerPlayerUUID))
            ? (new GetOrCreatePlayer())->run(uuid: $unbannerPlayerUUID)
            : null;

        $activeBan->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayer?->getKey(),
            'unban_type' => UnbanType::MANUAL->value,
            'unban_reason' => $reason,
        ]);

        PlayerUnbannedEvent::dispatch($activeBan);

        return $activeBan;
    }
}
