<?php

namespace App\Domains\Bans\Actions;

use App\Core\Domains\MinecraftPlayers\Actions\GetOrCreatePlayer;
use App\Core\Exceptions\ModelAlreadyExistsException;
use App\Domains\Bans\Events\PlayerBannedEvent;
use App\Domains\Bans\Transfers\CreatePlayerBanTransfer;
use App\Models\PlayerBan;
use Illuminate\Validation\ValidationException;

class CreatePlayerBan
{
    public function __construct(
        private readonly GetOrCreatePlayer $getOrCreatePlayer,
    ) {}

    /**
     * @throws ModelAlreadyExistsException if the player is already banned
     */
    public function call(CreatePlayerBanTransfer $input): PlayerBan {
        $bannedPlayer = $this->getOrCreatePlayer->call(uuid: $input->bannedPlayerUUID);

        $activeBanExists = PlayerBan::forPlayer($bannedPlayer)->active()->exists();
        if ($activeBanExists) {
            throw new ModelAlreadyExistsException(message: 'This UUID is already banned');
        }

        $bannerPlayer = ($input->bannerPlayerUUID != null)
            ? $this->getOrCreatePlayer->call(uuid: $input->bannerPlayerUUID)
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
