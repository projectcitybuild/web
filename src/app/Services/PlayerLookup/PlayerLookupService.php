<?php
namespace Domains\Services\PlayerLookup;

use App\Entities\Players\Repositories\MinecraftPlayerRepository;
use App\Entities\GamePlayerType;


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