<?php

namespace Shared\AccountLookup\Entities;

use App\Entities\Models\GameIdentifierType;

final class PlayerIdentifier
{
    public function __construct(
        public string $key,
        public GameIdentifierType $gameIdentifierType,
    ) {}
}
