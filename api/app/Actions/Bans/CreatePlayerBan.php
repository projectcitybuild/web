<?php

namespace App\Actions\Bans;

use App\Actions\GetOrCreatePlayer;
use App\Models\Eloquent\PlayerBan;
use App\Models\Events\PlayerBannedEvent;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class CreatePlayerBan
{
    /**
     * @throws ValidationException if $bannerPlayerUUID is already banned
     */
    public function create(
        String $bannedPlayerUUID,
        String $bannedPlayerAlias,
        ?String $bannerPlayerUUID,
        ?String $reason = null,
        ?Carbon $expiresAt = null,
    ): PlayerBan {
        $bannedPlayer = (new GetOrCreatePlayer())
            ->run(uuid: $bannedPlayerUUID);

        $activeBan = PlayerBan::forPlayer($bannedPlayer)->active();
        if ($activeBan->exists()) {
            throw ValidationException::withMessages([
                'banned_player_uuid' => ['This UUID is already banned'],
            ]);
        }

        $bannerPlayer = (! empty($bannerPlayerUUID))
            ? (new GetOrCreatePlayer())->run(uuid: $bannerPlayerUUID)
            : null;

        $ban = PlayerBan::create([
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => $bannedPlayerAlias,
            'banner_player_id' => $bannerPlayer?->getKey(),
            'reason' => empty($reason) ? null : $reason,
            'expires_at' => $expiresAt,
        ]);

        PlayerBannedEvent::dispatch($ban);

        return $ban;
    }
}
