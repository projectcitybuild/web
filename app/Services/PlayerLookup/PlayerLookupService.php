<?php

namespace App\Services\PlayerLookup;

use App\Entities\GamePlayerType;
use App\Entities\Players\Repositories\MinecraftPlayerRepository;

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
