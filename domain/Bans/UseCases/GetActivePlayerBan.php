<?php

namespace Domain\Bans\UseCases;

use Entities\Models\Eloquent\GamePlayerBan;
use Repositories\GamePlayerBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\ConcretePlayerLookup;

final class GetActivePlayerBan
{
    public function __construct(
        private readonly GamePlayerBanRepository $gamePlayerBanRepository,
        private readonly ConcretePlayerLookup    $playerLookup,
    ) {
    }

    /**
     * @param  PlayerIdentifier  $playerIdentifier
     * @return ?GamePlayerBan
     */
    public function execute(
        PlayerIdentifier $playerIdentifier,
    ): ?GamePlayerBan {
        $player = $this->playerLookup->find($playerIdentifier);
        if ($player === null) {
            return null;
        }

        return $this->gamePlayerBanRepository->firstActiveBan(player: $player);
    }
}
