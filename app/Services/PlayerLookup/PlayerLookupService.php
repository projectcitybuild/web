<?php

namespace App\Services\PlayerLookup;

use App\Entities\Models\GamePlayerType;
use App\Entities\Repositories\MinecraftPlayerRepository;

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
            case GamePlayerType::MINECRAFT:
                return $this->minecraftPlayerRepository->getByUuid($identifier)
                    ?: $this->minecraftPlayerRepository->store($identifier);
        }
    }
}
