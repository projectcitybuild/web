<?php
namespace App\Services\PlayerLookup;

use App\Entities\Eloquent\Players\Repositories\MinecraftPlayerRepository;
use App\Entities\Eloquent\GamePlayerType;


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