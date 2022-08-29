<?php

namespace App\Services\PlayerLookup;

use Repositories\MinecraftPlayerRepository;

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

    public function getOrCreatePlayer(string $identifier)
    {
        $identifier = str_replace('-', '', $identifier);

        return $this->minecraftPlayerRepository->getByUUID($identifier)
            ?: $this->minecraftPlayerRepository->store($identifier);
    }
}
