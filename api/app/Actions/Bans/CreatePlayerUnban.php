<?php

namespace App\Actions\Bans;

use App\Actions\GetOrCreatePlayer;
use App\Models\Eloquent\Player;
use App\Models\Eloquent\PlayerBan;
use App\Models\Events\PlayerUnbannedEvent;
use App\Models\Transfers\CreatePlayerUnbanTransfer;
use App\Models\UnbanType;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreatePlayerUnban
{
    /**
     * @throws ModelNotFoundException if $bannedPlayerUUID is not currently banned
     */
    public function create(CreatePlayerUnbanTransfer $input): PlayerBan
    {
        $bannedPlayer = Player::uuid($input->bannedPlayerUUID->rawValue())->firstOrFail();
        $activeBan = PlayerBan::forPlayer($bannedPlayer)->active()->firstOrFail();

        $unbannerPlayer = ($input->unbannerPlayerUUID != null)
            ? (new GetOrCreatePlayer())->run(uuid: $input->unbannerPlayerUUID)
            : null;

        $activeBan->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayer?->getKey(),
            'unban_type' => UnbanType::MANUAL->value,
            'unban_reason' => $input->reason,
        ]);

        PlayerUnbannedEvent::dispatch($activeBan);

        return $activeBan;
    }
}
