<?php

namespace Domain\Bans\UseCases;

use Entities\Models\Eloquent\GameBan;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\ConcretePlayerLookup;

final class GetActiveBan
{
    public function __construct(
        private readonly GameBanRepository $gameBanRepository,
        private readonly ConcretePlayerLookup $playerLookup,
    ) {
    }

    /**
     * @param  PlayerIdentifier  $playerIdentifier
     * @return ?GameBan
     */
    public function execute(
        PlayerIdentifier $playerIdentifier,
    ): ?GameBan {
        $player = $this->playerLookup->find($playerIdentifier);
        if ($player === null) {
            return null;
        }

        return $this->gameBanRepository->firstActiveBan(player: $player);
    }
}
