<?php

namespace Domain\Warnings\UseCases;

use Entities\Models\Eloquent\GameWarning;
use Repositories\GameBanRepository;
use Repositories\GameWarningRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;

final class CreateWarningUseCase
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
        private readonly GameWarningRepository $gameWarningRepository,
        private readonly GameBanRepository $gameBanRepository,
    ) {}

    public function execute(
        PlayerIdentifier $warnedPlayerIdentifier,
        string $warnedPlayerAlias,
        PlayerIdentifier $warnerPlayerIdentifier,
        string $warnerPlayerAlias,
        string $reason,
        float $weight,
    ): GameWarning {
        $warnedPlayer = $this->playerLookup->findOrCreate(
            identifier: $warnedPlayerIdentifier,
            playerAlias: $warnedPlayerAlias,
        );
        $warnerPlayer = $this->playerLookup->findOrCreate(
            identifier: $warnerPlayerIdentifier,
            playerAlias: $warnerPlayerAlias,
        );
        $warning = $this->gameWarningRepository->create(
            warnedPlayerId: $warnedPlayer->getKey(),
            warnerPlayerId: $warnerPlayer->getKey(),
            reason: $reason,
            weight: $weight,
        );

        $totalWeight = $this->gameWarningRepository->getTotalWeight(playerId: $warnedPlayer->getKey());
        if ($totalWeight >= 3) {
            $this->gameBanRepository->create(
                serverId:
            )
        }

        return $warning;
    }
}
