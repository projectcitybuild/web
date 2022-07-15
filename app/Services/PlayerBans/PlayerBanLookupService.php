<?php

namespace App\Services\PlayerBans;

use App\Services\PlayerLookup\PlayerLookupService;
use Repositories\GameBanV1Repository;

/**
 * @deprecated Use GetBanUseCase
 */
final class PlayerBanLookupService
{
    /**
     * @var GameBanV1Repository
     */
    private $gameBanRepository;

    /**
     * @var PlayerLookupService
     */
    private $playerLookupService;

    public function __construct(
        GameBanV1Repository $gameBanRepository,
        PlayerLookupService $playerLookupService
    ) {
        $this->gameBanRepository = $gameBanRepository;
        $this->playerLookupService = $playerLookupService;
    }

    public function getStatus(string $identifier)
    {
        $player = $this->playerLookupService->getOrCreatePlayer($identifier);

        return $this->gameBanRepository->getActiveBanByGameUserId($player->getKey());
    }

    public function getHistory(string $identifier)
    {
    }
}
