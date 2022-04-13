<?php

namespace Shared\AccountLookup;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\GameIdentifierType;
use App\Entities\Repositories\MinecraftPlayerRepository;
use Shared\AccountLookup\Exceptions\NoLinkedAccountException;
use Shared\AccountLookup\Exceptions\PlayerNotFoundException;

/**
 * @final
 */
class AccountLookup
{
    public function __construct(
        private MinecraftPlayerRepository $minecraftPlayerRepository,
    ) {}

    /**
     * @throws PlayerNotFoundException if identifier does not match any player
     * @throws NoLinkedAccountException if player is not linked to a PCB account
     */
    public function find(string $identifier, GameIdentifierType $identifierType): ?Account
    {
        $player = match ($identifierType) {
            GameIdentifierType::MINECRAFT_UUID => $this->minecraftPlayerRepository
                ->getByUuid($identifier),
        };

        if ($player === null) {
            throw new PlayerNotFoundException();
        }

        return $player->getLinkedAccount()
            ?? throw new NoLinkedAccountException();
    }
}
