<?php

namespace Domain\Warnings\UseCases;

use Illuminate\Database\Eloquent\Collection;
use Repositories\PlayerWarningRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class GetWarning
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
        private readonly PlayerWarningRepository $playerWarningRepository,
    ) {
    }

    public function execute(
        PlayerIdentifier $playerIdentifier,
        string $playerAlias,
    ): Collection {
        $player = $this->playerLookup->find(identifier: $playerIdentifier);

        return $this->playerWarningRepository->all(
            playerId: $player->getKey(),
        );
    }
}
