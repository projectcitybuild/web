<?php

namespace Domain\Bans\UseCases;

use Illuminate\Support\Collection;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\ConcretePlayerLookup;

final class GetAllBans
{
    public function __construct(
        private readonly GameBanRepository $gameBanRepository,
        private readonly ConcretePlayerLookup $playerLookup,
    ) {
    }

    /**
     * @param  PlayerIdentifier  $playerIdentifier
     * @return Collection
     */
    public function execute(
        PlayerIdentifier $playerIdentifier,
    ): Collection {
        $player = $this->playerLookup->find($playerIdentifier);
        if ($player === null) {
            return collect();
        }
        $bans = $this->gameBanRepository->all(player: $player);

        return $bans;
    }
}
