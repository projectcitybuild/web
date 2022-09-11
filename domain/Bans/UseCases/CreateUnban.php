<?php

namespace Domain\Bans\UseCases;

use Domain\Bans\Exceptions\NotBannedException;
use Domain\Bans\UnbanType;
use Entities\Models\Eloquent\GameBan;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

class CreateUnban
{
    public function __construct(
        private readonly GameBanRepository $gameBanRepository,
        private readonly PlayerLookup $playerLookup,
    ) {
    }

    /**
     * @param  PlayerIdentifier  $bannedPlayerIdentifier Player currently banned
     * @param  PlayerIdentifier  $unbannerPlayerIdentifier Player unbanning the banned player
     * @return GameBan
     *
     * @throws NotBannedException if the banned player is not actually banned
     */
    public function execute(
        PlayerIdentifier $bannedPlayerIdentifier,
        PlayerIdentifier $unbannerPlayerIdentifier,
        UnbanType $unbanType,
    ): GameBan {
        $player = $this->playerLookup->find(identifier: $bannedPlayerIdentifier)
            ?? throw new NotBannedException();

        $existingBan = $this->gameBanRepository->firstActiveBan(player: $player)
            ?? throw new NotBannedException();

        $unbannerPlayer = $this->playerLookup->findOrCreate(identifier: $unbannerPlayerIdentifier);

        $this->gameBanRepository->unban(
            ban: $existingBan,
            unbannerPlayerId: $unbannerPlayer->getKey(),
            unbanType: $unbanType,
        );

        return $existingBan->refresh();
    }
}
