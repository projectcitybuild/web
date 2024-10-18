<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Bans\Exceptions\AlreadyPermBannedException;
use App\Domains\Bans\Exceptions\AlreadyTempBannedException;
use App\Domains\MinecraftEventBus\Events\MinecraftUuidBanned;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Carbon;

final class CreatePlayerBan
{
    public function execute(
        int $serverId,
        MinecraftUUID $bannedPlayerUuid,
        string $bannedPlayerAlias,
        MinecraftUUID $bannerPlayerUuid,
        string $bannerPlayerAlias,
        ?string $banReason,
        ?Carbon $expiresAt,
    ): GamePlayerBan {
        $bannedPlayer = MinecraftPlayer::firstOrCreate(
            uuid: $bannedPlayerUuid,
            alias: $bannedPlayerAlias,
        );

        $existingBan = GamePlayerBan::where('banned_player_id', $bannedPlayer->getKey())
            ->active()
            ->first();

        if ($existingBan !== null) {
            if ($existingBan->isTemporaryBan()) {
                throw new AlreadyTempBannedException();
            }
            throw new AlreadyPermBannedException();
        }

        $bannerPlayer = MinecraftPlayer::firstOrCreate(
            uuid: $bannerPlayerUuid,
            alias: $bannerPlayerAlias,
        );

        $ban = GamePlayerBan::create([
            'server_id' => $serverId,
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => $bannedPlayerAlias,
            'banner_player_id' => $bannerPlayer->getKey(),
            'reason' => $banReason,
            'expires_at' => $expiresAt,
        ]);

        MinecraftUuidBanned::dispatch($ban);

        return $ban;
    }
}
