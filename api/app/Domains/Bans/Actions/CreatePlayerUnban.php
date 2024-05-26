<?php

namespace App\Domains\Bans\Actions;

use App\Core\MinecraftPlayers\Actions\GetOrCreatePlayer;
use App\Domains\Bans\Events\PlayerUnbannedEvent;
use App\Domains\Bans\Transfers\CreatePlayerUnbanTransfer;
use App\Domains\Bans\UnbanType;
use App\Models\Player;
use App\Models\PlayerBan;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreatePlayerUnban
{
    public function __construct(
        private readonly GetOrCreatePlayer $getOrCreatePlayer,
    ) {}

    /**
     * @throws ModelNotFoundException if $bannedPlayerUUID is not currently banned
     */
    public function call(CreatePlayerUnbanTransfer $input): PlayerBan
    {
        $bannedPlayer = Player::uuid($input->bannedPlayerUUID)->firstOrFail();
        $activeBan = PlayerBan::forPlayer($bannedPlayer)->active()->firstOrFail();

        $unbannerPlayer = ($input->unbannerPlayerUUID != null)
            ? $this->getOrCreatePlayer->call(uuid: $input->unbannerPlayerUUID)
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
