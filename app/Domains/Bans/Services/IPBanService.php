<?php

namespace App\Domains\Bans\Services;

use App\Domains\Bans\Data\CreateIPBan;
use App\Domains\Bans\Data\DeleteIPBan;
use App\Domains\Bans\Exceptions\AlreadyIPBannedException;
use App\Domains\Bans\Exceptions\NotIPBannedException;
use App\Domains\MinecraftEventBus\Events\IpAddressBanned;
use App\Models\GameIPBan;
use App\Models\MinecraftPlayer;

class IPBanService
{
    public function find(string $ip): ?GameIPBan
    {
        return GameIPBan::where('ip_address', $ip)
            ->whereNull('unbanned_at')
            ->first();
    }

    public function create(CreateIPBan $req): GameIPBan
    {
        throw_if(
            GameIPBan::where('ip_address', $req->ip)
                ->whereNull('unbanned_at')
                ->exists(),
            AlreadyIPBannedException::class,
        );

        $bannerPlayer = MinecraftPlayer::firstOrCreate(
            uuid: $req->bannerUuid,
            alias: $req->bannerAlias,
        );

        $ban = GameIPBan::create([
            'ip_address' => $req->ip,
            'reason' => $req->reason,
            'banner_player_id' => $bannerPlayer->getKey(),
        ]);

        IpAddressBanned::dispatch($ban);

        return $ban;
    }

    public function delete(DeleteIPBan $req): GameIPBan
    {
        $existingBan = GameIPBan::where('ip_address', $req->ip)
            ->whereNull('unbanned_at')
            ->first()
            ?? throw new NotIPBannedException();

        $unbannerPlayer = MinecraftPlayer::firstOrCreate(uuid: $req->unbannerUuid);

        $existingBan->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayer->getKey(),
            'unban_type' => $req->unbanType->value,
        ]);

        return $existingBan->refresh();
    }
}
