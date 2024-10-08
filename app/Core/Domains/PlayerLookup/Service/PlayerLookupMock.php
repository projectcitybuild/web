<?php

namespace App\Core\Domains\PlayerLookup\Service;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Player;

/** @deprecated */
final class PlayerLookupMock implements PlayerLookup
{
    public ?Player $find;
    public Player $findOrCreate;

    public function find(PlayerIdentifier $identifier): ?Player
    {
        return $this->find;
    }

    public function findOrCreate(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player {
        return $this->findOrCreate;
    }
}
