<?php

namespace App\Domains\Warnings\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Models\PlayerWarning;
use Illuminate\Support\Collection;

final class GetWarnings
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {}

    public function execute(
        PlayerIdentifier $playerIdentifier,
        ?string $playerAlias = null, // TODO: use this later for nickname syncing
    ): Collection {
        $player = $this->playerLookup->find(identifier: $playerIdentifier);
        if ($player === null) {
            return collect();
        }

        return PlayerWarning::where('warned_player_id', $player->getKey())
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
