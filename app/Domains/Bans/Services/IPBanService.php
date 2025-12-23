<?php

namespace App\Domains\Bans\Services;

use App\Domains\Bans\Data\CreateIPBan;
use App\Domains\Bans\Data\DeleteIPBan;
use App\Domains\Bans\Exceptions\AlreadyBannedException;
use App\Domains\Bans\Exceptions\BanNotFoundException;
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
            new AlreadyBannedException('IP address already banned ('.$req->ip.')'),
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

    // TODO: generalize into update and delete
    public function delete(DeleteIPBan $req): GameIPBan
    {
        $existingBan = GameIPBan::where('ip_address', $req->ip)
            ->whereNull('unbanned_at')
            ->first()
            ?? throw new BanNotFoundException;

        $unbannerPlayer = MinecraftPlayer::firstOrCreate(uuid: $req->unbannerUuid);

        $existingBan->update([
            'unbanned_at' => now(),
            'unbanner_player_id' => $unbannerPlayer->getKey(),
            'unban_type' => $req->unbanType->value,
        ]);

        return $existingBan->refresh();
    }
}
