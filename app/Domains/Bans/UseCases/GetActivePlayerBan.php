<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Models\GamePlayerBan;

final class GetActivePlayerBan
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {}

    public function execute(
        PlayerIdentifier $playerIdentifier,
    ): ?GamePlayerBan {
        $player = $this->playerLookup->find($playerIdentifier);
        if ($player === null) {
            return null;
        }

        return GamePlayerBan::where('banned_player_id', $player->getKey())
            ->active()
            ->first();
    }
}
