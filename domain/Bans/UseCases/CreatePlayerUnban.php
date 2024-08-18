<?php

namespace Domain\Bans\UseCases;

use App\Models\GamePlayerBan;
use Domain\Bans\Exceptions\NotBannedException;
use Domain\Bans\UnbanType;
use Repositories\GamePlayerBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\PlayerLookup;

class CreatePlayerUnban
{
    public function __construct(
        private readonly GamePlayerBanRepository $gamePlayerBanRepository,
        private readonly PlayerLookup $playerLookup,
    ) {
    }

    /**
     * @param  PlayerIdentifier  $bannedPlayerIdentifier Player currently banned
     * @param  PlayerIdentifier  $unbannerPlayerIdentifier Player unbanning the banned player
     * @return GamePlayerBan
     *
     * @throws NotBannedException if the banned player is not actually banned
     */
    public function execute(
        PlayerIdentifier $bannedPlayerIdentifier,
        PlayerIdentifier $unbannerPlayerIdentifier,
        UnbanType $unbanType,
    ): GamePlayerBan {
        $player = $this->playerLookup->find(identifier: $bannedPlayerIdentifier)
            ?? throw new NotBannedException();

        $existingBan = $this->gamePlayerBanRepository->firstActiveBan(player: $player)
            ?? throw new NotBannedException();

        $unbannerPlayer = $this->playerLookup->findOrCreate(identifier: $unbannerPlayerIdentifier);

        $this->gamePlayerBanRepository->unban(
            ban: $existingBan,
            unbannerPlayerId: $unbannerPlayer->getKey(),
            unbanType: $unbanType,
        );

        return $existingBan->refresh();
    }
}
