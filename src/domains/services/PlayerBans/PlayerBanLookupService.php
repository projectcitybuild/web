<?php
namespace Domains\Services\PlayerBans;

use Domains\Modules\GameIdentifierType;
use Domains\Modules\Bans\Repositories\GameBanRepository;
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


    public function getStatus(GameIdentifierType $identifierType, string $identifier)
    {
        $player = $this->playerLookupService->getOrCreatePlayer($identifierType->playerType(), $identifier);

        return $this->gameBanRepository->getActiveBanByGameUserId($player->getKey(), 
                                                                  $identifierType->playerType());
    }

    public function getHistory(GameIdentifierType $identifierType, string $identifier)
    {

    }
}