<?php

namespace Domain\Warnings\UseCases;

use Illuminate\Support\Collection;
use Repositories\Warnings\PlayerWarningRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class GetWarnings
{
    public function __construct(
        private readonly PlayerLookup            $playerLookup,
        private readonly PlayerWarningRepository $playerWarningRepository,
    ) {
    }

    public function execute(
        PlayerIdentifier $playerIdentifier,
        ?string $playerAlias = null, // TODO: use this later for nickname syncing
    ): Collection {
        $player = $this->playerLookup->find(identifier: $playerIdentifier);
        if ($player === null) {
            return collect();
        }

        return $this->playerWarningRepository->all(
            playerId: $player->getKey(),
        );
    }
}
