<?php

namespace Domain\Bans\UseCases;

use Entities\Models\Eloquent\GameBan;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class GetBanUseCase
{
    public function __construct(
        private GameBanRepository $gameBanRepository,
        private PlayerLookup $playerLookup,
    ) {
    }

    /**
     * @param  PlayerIdentifier  $playerIdentifier
     * @return ?GameBan
     */
    public function execute(
        PlayerIdentifier $playerIdentifier,
    ): ?GameBan {
        $mcPlayer = $this->playerLookup->findOrCreate($playerIdentifier);
        return $this->gameBanRepository->firstActiveBan($mcPlayer);
    }
}
