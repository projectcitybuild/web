<?php

namespace Shared\PlayerLookup\Service;

use Shared\PlayerLookup\Contracts\Player;
use Shared\PlayerLookup\Entities\PlayerIdentifier;

interface PlayerLookup
{
    public function find(PlayerIdentifier $identifier): ?Player;

    public function findOrCreate(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player;
}
