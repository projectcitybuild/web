<?php
namespace Domains\Services\PlayerBans;

use Entities\GamePlayerType;
use Entities\Bans\Repositories\GameBanRepository;
use Domains\Services\PlayerLookup\PlayerLookupService;


class PlayerBanLookupService
{
    /**
     * @var GameBanRepository
     */
    private $gameBanRepository;

    /**
     * @var PlayerLookupService
     */
    private $playerLookupService;


    public function __construct(GameBanRepository $gameBanRepository,
                                PlayerLookupService $playerLookupService)
    {
        $this->gameBanRepository = $gameBanRepository;
        $this->playerLookupService = $playerLookupService;
    }


    public function getStatus(GamePlayerType $playerType, string $identifier)
    {
        $player = $this->playerLookupService->getOrCreatePlayer($playerType, $identifier);
        
        return $this->gameBanRepository->getActiveBanByGameUserId($player->getKey(), 
                                                                  $playerType);
    }

    public function getHistory(GamePlayerType $playerType, string $identifier)
    {

    }
}