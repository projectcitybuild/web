<?php

namespace App\Services\PlayerLookup;

use Entities\Models\GamePlayerType;
use Entities\Repositories\MinecraftPlayerRepository;

/**
 * @deprecated
 */
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
                return $this->minecraftPlayerRepository->getByUUID($identifier)
                    ?: $this->minecraftPlayerRepository->store($identifier);
        }
    }
}
