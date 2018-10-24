<?php
namespace Domains\Services\PlayerLookup;

use Entities\Players\Repositories\MinecraftPlayerRepository;
use Entities\GamePlayerType;


class PlayerLookupService
{
    /**
     * @var MinecraftPlayerRepository
     */
    private $minecraftPlayerRepository;


    public function __construct(MinecraftPlayerRepository $minecraftPlayerRepository)
    {
        $this->minecraftPlayerRepository = $minecraftPlayerRepository;
    }

    public function getOrCreatePlayer(GamePlayerType $playerType, string $identifier)
    {
        switch ($playerType) {
            case GamePlayerType::Minecraft:
                return $this->minecraftPlayerRepository->getByUuid($identifier)
                    ?: $this->minecraftPlayerRepository->store($identifier);
        }
      
    }
}