<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Bans\Exceptions\AlreadyIPBannedException;
use App\Domains\MinecraftEventBus\Events\IpAddressBanned;
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

        throw_if($existingBan !== null, AlreadyIPBannedException::class);

        $bannerPlayer = MinecraftPlayer::firstOrCreate(
            uuid: $bannerPlayerUuid,
            alias: $bannerPlayerAlias,
        );

        $ban = GameIPBan::create([
            'banner_player_id' => $bannerPlayer->getKey(),
            'ip_address' => $ip,
            'reason' => $banReason,
        ]);

        IpAddressBanned::dispatch($ban);

        return $ban;
    }
}
