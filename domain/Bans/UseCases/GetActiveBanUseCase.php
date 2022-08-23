<?php

namespace Domain\Bans\UseCases;

use Entities\Models\Eloquent\GameBan;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class GetActiveBanUseCase
{
    public function __construct(
        private readonly GameBanRepository $gameBanRepository,
        private readonly PlayerLookup $playerLookup,
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

        return $this->gameBanRepository->firstActiveBan(player: $mcPlayer, skipTempBans: false);
    }
}
