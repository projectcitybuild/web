<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use Illuminate\Support\Collection;
use Repositories\GamePlayerBanRepository;

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
