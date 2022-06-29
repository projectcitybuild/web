<?php

namespace Domain\Bans\UseCases;

use Entities\Models\Eloquent\GameBan;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class GetBanUseCase
{
    public function __construct(
        private GameBanRepository $gameBanRepository,
    ) {
    }

    /**
     * @param  PlayerIdentifier  $playerIdentifier
     * @return ?GameBan
     */
    public function execute(
        PlayerIdentifier $playerIdentifier,
    ): ?GameBan {
        return $this->gameBanRepository->firstActiveBan(identifier: $playerIdentifier);
    }
}
