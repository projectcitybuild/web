<?php

namespace App\Services\PlayerBans;

use App\Entities\Eloquent\GamePlayerType;
use App\Entities\Eloquent\Bans\Repositories\GameBanRepository;
use App\Services\PlayerLookup\PlayerLookupService;

final class PlayerBanLookupService
{
    /**
     * @var GameBanRepository
     */
    private $gameBanRepository;

    /**
     * @var PlayerLookupService
     */
    private $playerLookupService;


    public function __construct(
        GameBanRepository $gameBanRepository,
        PlayerLookupService $playerLookupService
    ) {
        $this->gameBanRepository = $gameBanRepository;
        $this->playerLookupService = $playerLookupService;
    }


    public function getStatus(GamePlayerType $playerType, string $identifier)
    {
        $player = $this->playerLookupService->getOrCreatePlayer($playerType, $identifier);
        
        return $this->gameBanRepository->getActiveBanByGameUserId($player->getKey(), $playerType);
    }

    public function getHistory(GamePlayerType $playerType, string $identifier)
    {

    }
}