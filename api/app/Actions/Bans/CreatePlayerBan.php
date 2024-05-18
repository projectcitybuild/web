<?php

namespace App\Actions\Bans;

use App\Actions\GetOrCreatePlayer;
use App\Models\Eloquent\PlayerBan;
use App\Models\Events\PlayerBannedEvent;
use App\Models\Transfers\CreatePlayerBanTransfer;
use Illuminate\Validation\ValidationException;

class CreatePlayerBan
{
    /**
     * @throws ValidationException if $bannerPlayerUUID is already banned
     */
    public function create(CreatePlayerBanTransfer $input): PlayerBan {
        $bannedPlayer = (new GetOrCreatePlayer())
            ->run(uuid: $input->bannedPlayerUUID);

        $activeBan = PlayerBan::forPlayer($bannedPlayer)->active();
        if ($activeBan->exists()) {
            throw ValidationException::withMessages([
                'banned_player_uuid' => ['This UUID is already banned'],
            ]);
        }

        $bannerPlayer = ($input->bannerPlayerUUID != null)
            ? (new GetOrCreatePlayer())->run(uuid: $input->bannerPlayerUUID)
            : null;

        $ban = PlayerBan::create([
            'banned_player_id' => $bannedPlayer->getKey(),
            'banned_alias_at_time' => $input->bannedPlayerAlias,
            'banner_player_id' => $bannerPlayer?->getKey(),
            'reason' => empty($input->reason) ? null : $input->reason,
            'expires_at' => $input->expiresAt,
        ]);

        PlayerBannedEvent::dispatch($ban);

        return $ban;
    }
}
