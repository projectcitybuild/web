<?php

namespace Domain\Bans\UseCases;

use App\Models\GamePlayerBan;
use Repositories\GamePlayerBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\PlayerLookup;

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
