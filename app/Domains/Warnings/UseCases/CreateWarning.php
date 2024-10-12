<?php

namespace App\Domains\Warnings\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Models\PlayerWarning;

final class CreateWarning
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {}

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

        return PlayerWarning::create([
            'warned_player_id' => $warnedPlayer->getKey(),
            'warner_player_id' => $warnerPlayer->getKey(),
            'reason' => $reason,
            'weight' => $weight,
            'is_acknowledged' => $isAcknowledged,
        ]);
    }
}
