<?php

namespace App\Http\Actions\GameBans;

use App\Entities\Bans\Models\GameBan;
use App\Services\PlayerBans\Exceptions\UnauthorisedKeyActionException;
use App\Services\PlayerBans\Exceptions\UserAlreadyBannedException;
use App\Http\Actions\Players\GetOrCreatePlayer;

final class CreatePlayerBan
{
    private $getOrCreatePlayer;

    public function __construct(GetOrCreatePlayer $getOrCreatePlayer)
    {
        $this->getOrCreatePlayer = $getOrCreatePlayer;
    }

    public function execute(
        ServerKey $serverKey,
        int $serverId,
        string $bannedPlayerId,
        GamePlayerType $bannedPlayerType,
        string $bannedAliasAtTime,
        string $staffPlayerId,
        GamePlayerType $staffPlayerType,
        ?string $banReason,
        ?int $banExpiresAt = null,
        bool $isGlobalBan = false
    ) : GameBan {
        if ($isGlobalBan && !$serverKey->can_global_ban) {
            throw new UnauthorisedKeyActionException('limited_key', 'This server key does not have permission to create global bans');
        }
 
         $bannedPlayer = $this->getOrCreatePlayer($bannedPlayerType, $bannedPlayerId);
         $staffPlayer  = $this->getOrCreatePlayer($staffPlayerType, $staffPlayerId);
         
         $activeBan = $this->getActiveBanByGameUserId($bannedPlayer->getKey(), $bannedPlayerType);
 
         if ($activeBan !== null) {
             throw new UserAlreadyBannedException('player_already_banned', 'Player is already banned');
         }

         return GameBan::create([
            'server_id'             => $serverId,
            'banned_player_id'      => $bannedPlayer->getKey(),
            'banned_player_type'    => $bannedPlayerType->valueOf(),
            'banned_alias_at_time'  => $bannedAliasAtTime,
            'staff_player_id'       => $staffPlayer->getKey(),
            'staff_player_type'     => $staffPlayerType->valueOf(),
            'reason'                => $banReason,
            'is_active'             => true,
            'is_global_ban'         => $isGlobalBan,
            'expires_at'            => $banExpiresAt ? Carbon::createFromTimestamp($banExpiresAt) : null,
        ]);
    }

    private function getActiveBanByGameUserId(
        int $bannedPlayerId,
        GamePlayerType $bannedPlayerType,
        int $serverId = null,
        array $with = []
    ) : ?GameBan {
        return GameBan::with($with)
            ->where('banned_player_id', $bannedPlayerId)
            ->where('banned_player_type', $bannedPlayerType->valueOf())
            ->where('is_active', true)
            ->when(isset($serverId),
                function ($q) use ($serverId) {
                    return $q->where('server_id', $serverId)
                             ->orWhere('is_global_ban', true);
                },
                function ($q) {
                    return $q->where('is_global_ban', true);
                }
            )
            ->first();
    }
}