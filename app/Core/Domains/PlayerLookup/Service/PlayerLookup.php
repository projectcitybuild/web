<?php

namespace App\Core\Domains\PlayerLookup\Service;

use App\Core\Domains\PlayerLookup\Contracts\Player;
use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;

interface PlayerLookup
{
    public function find(PlayerIdentifier $identifier): ?Player;

    public function findOrCreate(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player;
}
