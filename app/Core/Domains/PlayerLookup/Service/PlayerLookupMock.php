<?php

namespace App\Core\Domains\PlayerLookup\Service;

use App\Core\Domains\PlayerLookup\Contracts\Player;
use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;

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
