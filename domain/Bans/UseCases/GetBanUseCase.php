<?php

namespace Domain\Bans\UseCases;

use App\Entities\Models\Eloquent\GameBan;
use Domain\Bans\Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class GetBanUseCase
{
    public function __construct(
        private GameBanRepository $gameBanRepository,
    ) {}

    /**
     * @param PlayerIdentifier $playerIdentifier
     * @return ?GameBan
     */
    public function execute(
        PlayerIdentifier $playerIdentifier,
    ): ?GameBan {
        return $this->gameBanRepository->firstActiveBan(identifier: $playerIdentifier);
    }
}
