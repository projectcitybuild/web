<?php

namespace App\Domains\Bans\Actions;

use App\Core\Domains\MinecraftPlayers\Actions\GetOrCreatePlayer;
use App\Core\Exceptions\ModelAlreadyExistsException;
use App\Domains\Bans\Transfers\CreateIPBanTransfer;
use App\Models\IPBan;

class CreateIPBan
{
    public function __construct(
        private readonly GetOrCreatePlayer $getOrCreatePlayer,
    ) {}

    /**
     * @throws ModelAlreadyExistsException if the ip address is already banned
     */
    public function call(CreateIPBanTransfer $input): IPBan {
        $activeBanExists = IPBan::forIP($input->ip)->active()->exists();
        if ($activeBanExists) {
            throw new ModelAlreadyExistsException(message: 'This UUID is already banned');
        }

        $bannerPlayer = ($input->bannerPlayerUUID != null)
            ? $this->getOrCreatePlayer->call(uuid: $input->bannerPlayerUUID)
            : null;

        return IPBan::create([
            'ip_address' => $input->ip,
            'banner_player_id' => $bannerPlayer?->getKey(),
            'reason' => empty($input->reason) ? null : $input->reason,
        ]);
    }
}
