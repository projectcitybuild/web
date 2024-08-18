<?php

namespace App\Core\Domains\PlayerLookup\Service;

use App\Core\Domains\PlayerLookup\Contracts\Player;
use App\Core\Domains\PlayerLookup\Entities\PlayerIdentifier;

interface PlayerLookup
{
    public function find(PlayerIdentifier $identifier): ?Player;

    public function findOrCreate(
        PlayerIdentifier $identifier,
        ?string $playerAlias = null,
    ): Player;
}
