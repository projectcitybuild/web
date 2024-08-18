<?php

namespace App\Domains\Bans\UseCases;

use App\Core\Domains\PlayerLookup\Entities\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Models\GamePlayerBan;
use Repositories\GamePlayerBanRepository;

final class GetActivePlayerBan
{
    public function __construct(
        private readonly GamePlayerBanRepository $gamePlayerBanRepository,
        private readonly PlayerLookup $playerLookup,
    ) {
    }

    public function execute(
        PlayerIdentifier $playerIdentifier,
    ): ?GamePlayerBan {
        $player = $this->playerLookup->find($playerIdentifier);
        if ($player === null) {
            return null;
        }

        return $this->gamePlayerBanRepository->firstActiveBan(player: $player);
    }
}
