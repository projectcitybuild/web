<?php

namespace Domain\Bans\UseCases;

use Illuminate\Database\Eloquent\Collection;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class GetAllBansUseCase
{
    public function __construct(
        private readonly GameBanRepository $gameBanRepository,
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
        return $this->gameBanRepository->all(
            player: $this->playerLookup->findOrCreate($playerIdentifier),
        );
    }
}
