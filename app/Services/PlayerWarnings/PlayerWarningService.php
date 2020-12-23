<?php

namespace App\Services\PlayerWarnings;

use App\Entities\GamePlayerType;
use App\Entities\Warnings\Repositories\GameWarningRepository;
use App\Services\PlayerLookup\PlayerLookupService;

class PlayerWarningService
{
    private GameWarningRepository $gameWarningRepository;

    private PlayerLookupService $playerLookupService;

    public function __construct(GameWarningRepository $gameWarningRepository,
                                PlayerLookupService $playerLookupService)
    {
        $this->gameWarningRepository = $gameWarningRepository;
        $this->playerLookupService = $playerLookupService;
    }

    public function warn(string $warnedPlayerId,
                         GamePlayerType $warnedPlayerType,
                         string $staffPlayerId,
                         GamePlayerType $staffPlayerType,
                         string $reason,
                         int $serverId,
                         int $weight = 1)
    {
        $warnedPlayer = $this->playerLookupService->getOrCreatePlayer($warnedPlayerType, $warnedPlayerId);
        $staffPlayer = $this->playerLookupService->getOrCreatePlayer($staffPlayerType, $staffPlayerId);

        return $this->gameWarningRepository->store($serverId,
                                                   $warnedPlayer->getKey(),
                                                   $warnedPlayerType,
                                                   $staffPlayer->getKey(),
                                                   $staffPlayerType,
                                                   $reason,
                                                   $weight,
                                                   true);
    }

    public function getWarningCount(string $warnedPlayerId,
                                    GamePlayerType $warnedPlayerType,
                                    int $serverId)
    {
        $warnedPlayer = $this->playerLookupService->getOrCreatePlayer($warnedPlayerType, $warnedPlayerId);

        return $this->gameWarningRepository->getCount($serverId,
                                                      $warnedPlayer->getKey(),
                                                      $warnedPlayerType);
    }
}
