<?php

namespace App\Domains\Warnings\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use Illuminate\Support\Collection;
use Repositories\PlayerWarnings\PlayerWarningRepository;

final class GetWarnings
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
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
