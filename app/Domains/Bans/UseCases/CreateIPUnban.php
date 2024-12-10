<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\Bans\Exceptions\NotIPBannedException;
use App\Models\GameIPBan;
use App\Models\MinecraftPlayer;

class CreateIPUnban
{
    public function execute(
        string $ip,
        MinecraftUUID $unbannerPlayerUuid,
        UnbanType $unbanType,
    ): GameIPBan {
        $existingBan = GameIPBan::where('ip_address', $ip)
            ->whereNull('unbanned_at')
            ->first()
            ?? throw new NotIPBannedException();

        $unbannerPlayer = MinecraftPlayer::firstOrCreate(uuid: $unbannerPlayerUuid);

        $existingBan->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayer->getKey(),
            'unban_type' => $unbanType->value,
        ]);

        return $existingBan->refresh();
    }
}
