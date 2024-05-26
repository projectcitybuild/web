<?php

namespace App\Domains\Bans\Actions;

use App\Core\Domains\MinecraftPlayers\Actions\GetOrCreatePlayer;
use App\Domains\Bans\Transfers\CreateIPUnbanTransfer;
use App\Domains\Bans\UnbanType;
use App\Models\IPBan;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateIPUnban
{
    public function __construct(
        private readonly GetOrCreatePlayer $getOrCreatePlayer,
    ) {}

    /**
     * @throws ModelNotFoundException if $bannedPlayerUUID is not currently banned
     */
    public function call(CreateIPUnbanTransfer $input): IPBan
    {
        $activeBan = IPBan::forIP($input->ip)->active()->firstOrFail();

        $unbannerPlayer = ($input->unbannerPlayerUUID != null)
            ? $this->getOrCreatePlayer->call(uuid: $input->unbannerPlayerUUID)
            : null;

        $activeBan->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayer?->getKey(),
            'unban_type' => UnbanType::MANUAL->value,
        ]);

        return $activeBan;
    }
}
