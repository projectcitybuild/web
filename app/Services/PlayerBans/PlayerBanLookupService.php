<?php

namespace App\Services\PlayerBans;

use App\Services\PlayerLookup\PlayerLookupService;
use Entities\Models\GamePlayerType;
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

    public function getStatus(GamePlayerType $playerType, string $identifier)
    {
        if ($playerType === GamePlayerType::MINECRAFT) {
            // Strip hyphens from Minecraft UUIDs
            $identifier = str_replace('-', '', $identifier);
        }

        $player = $this->playerLookupService->getOrCreatePlayer($playerType, $identifier);

        return $this->gameBanRepository->getActiveBanByGameUserId($player->getKey(), $playerType);
    }

    public function getHistory(GamePlayerType $playerType, string $identifier)
    {
    }
}
