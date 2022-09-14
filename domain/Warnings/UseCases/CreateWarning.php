<?php

namespace Domain\Warnings\UseCases;

use Entities\Models\Eloquent\PlayerWarning;
use Repositories\Warnings\PlayerWarningRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\PlayerLookup;

final class CreateWarning
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
        private readonly PlayerWarningRepository $playerWarningRepository,
    ) {
    }

    public function execute(
        PlayerIdentifier $warnedPlayerIdentifier,
        string $warnedPlayerAlias,
        PlayerIdentifier $warnerPlayerIdentifier,
        string $warnerPlayerAlias,
        string $reason,
        float $weight,
        bool $isAcknowledged,
    ): PlayerWarning {
        $warnedPlayer = $this->playerLookup->findOrCreate(
            identifier: $warnedPlayerIdentifier,
            playerAlias: $warnedPlayerAlias,
        );
        $warnerPlayer = $this->playerLookup->findOrCreate(
            identifier: $warnerPlayerIdentifier,
            playerAlias: $warnerPlayerAlias,
        );

        return $this->playerWarningRepository->create(
            warnedPlayerId: $warnedPlayer->getKey(),
            warnerPlayerId: $warnerPlayer->getKey(),
            reason: $reason,
            weight: $weight,
            isAcknowledged: $isAcknowledged,
        );
    }
}
