<?php

namespace Shared\PlayerLookup\Service;

use Shared\PlayerLookup\Contracts\Player;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

final class MockPlayerLookup implements PlayerLookup
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
