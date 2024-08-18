<?php

namespace App\Domains\Bans\UseCases;

use Illuminate\Support\Collection;
use Repositories\GamePlayerBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\PlayerLookup;

final class GetAllPlayerBans
{
    public function __construct(
        private readonly GamePlayerBanRepository $gamePlayerBanRepository,
        private readonly PlayerLookup $playerLookup,
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
        $bans = $this->gamePlayerBanRepository->all(player: $player);

        return $bans;
    }
}
