<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Bans\Exceptions\AlreadyIPBannedException;
use App\Models\GameIPBan;
use App\Models\MinecraftPlayer;

final class CreateIPBan
{
    public function execute(
        string $ip,
        MinecraftUUID $bannerPlayerUuid,
        string $bannerPlayerAlias,
        string $banReason,
    ): GameIPBan {
        $existingBan = GameIPBan::where('ip_address', $ip)
            ->whereNull('unbanned_at')
            ->first();

        if ($existingBan !== null) {
            throw new AlreadyIPBannedException();
        }

        $bannerPlayer = MinecraftPlayer::firstOrCreate(
            uuid: $bannerPlayerUuid,
            alias: $bannerPlayerAlias,
        );

        return GameIPBan::create([
            'banner_player_id' => $bannerPlayer->getKey(),
            'ip_address' => $ip,
            'reason' => $banReason,
        ]);
    }
}
