<?php

namespace App\Core\Domains\PlayerLookup\Service;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Player;

/** @deprecated */
interface PlayerLookup
{
    public function find(PlayerIdentifier $identifier): ?Player;

    public function findOrCreate(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player;
}
