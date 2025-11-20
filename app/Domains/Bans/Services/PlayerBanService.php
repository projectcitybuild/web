<?php

namespace App\Domains\Bans\Services;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Bans\Data\CreatePlayerBan;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\Bans\Data\UpdatePlayerBan;
use App\Domains\Bans\Exceptions\AlreadyPlayerBannedException;
use App\Domains\MinecraftEventBus\Events\MinecraftUuidBanned;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Collection;

class PlayerBanService
{
    public function store(CreatePlayerBan $req): GamePlayerBan
    {
        $bannedPlayer = MinecraftPlayer::firstOrCreate($req->bannedUuid, alias: $req->bannedAlias);

        throw_if(
            GamePlayerBan::where('banned_player_id', $bannedPlayer->getKey())
                ->active()
                ->exists(),
            AlreadyPlayerBannedException::class,
        );

        $bannerPlayer = optional(
            $req->bannerUuid,
            fn($uuid) => MinecraftPlayer::firstOrCreate($uuid, alias: $req->bannerAlias),
        );
        $unbannerPlayer = optional(
            $req->unbannerUuid,
            fn($uuid) => MinecraftPlayer::firstOrCreate($uuid, alias: $req->unbannerAlias),
        );

        $ban = GamePlayerBan::create([
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => $req->bannedAlias,
            'banner_player_id' => $bannerPlayer?->getKey(),
            'reason' => $req->reason,
            'additional_info' => $req->additionalInfo,
            'expires_at' => $req->expiresAt,
            'created_at' => $req->createdAt,
            'unbanned_at' => $req->unbannedAt,
            'unbanner_player_id' => $unbannerPlayer?->getKey(),
            'unban_type' => $req->unbanType,
        ]);

        MinecraftUuidBanned::dispatch($ban);

        return $ban;
    }

    public function update(UpdatePlayerBan $req): GamePlayerBan
    {
        $bannedPlayer = MinecraftPlayer::firstOrCreate($req->bannedUuid, alias: $req->bannedAlias);

        $bannerPlayer = optional(
            $req->bannerUuid,
            fn($uuid) => MinecraftPlayer::firstOrCreate($uuid, alias: $req->bannerAlias),
        );
        $unbannerPlayer = optional(
            $req->unbannerUuid,
            fn($uuid) => MinecraftPlayer::firstOrCreate($uuid, alias: $req->unbannerAlias),
        );

        $ban = GamePlayerBan::findOrFail($req->id);
        $ban->update([
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => $req->bannedAlias,
            'banner_player_id' => $bannerPlayer?->getKey(),
            'reason' => $req->reason,
            'additional_info' => $req->additionalInfo,
            'expires_at' => $req->expiresAt,
            'created_at' => $req->createdAt,
            'updated_at' => now(),
            'unbanned_at' => $req->unbannedAt,
            'unbanner_player_id' => $unbannerPlayer?->getKey(),
            'unban_type' => UnbanType::MANUAL,
        ]);
        $ban = $ban->refresh();

        MinecraftUuidBanned::dispatch($ban);

        return $ban;
    }

    public function delete(int $id)
    {
        $ban = GamePlayerBan::findOrFail($id);
        $ban->delete();
    }

    public function all(MinecraftUUID $playerUuid): Collection
    {
        $player = MinecraftPlayer::whereUuid($playerUuid)->first();
        if ($player === null) {
            return collect();
        }
        return GamePlayerBan::where('banned_player_id', $player->getKey())->get();
    }

    public function firstActive(MinecraftUUID $playerUuid): ?GamePlayerBan
    {
        $player = MinecraftPlayer::whereUuid($playerUuid)->first();
        if ($player === null) {
            return null;
        }
        return GamePlayerBan::where('banned_player_id', $player->getKey())
            ->active()
            ->first();
    }
}
