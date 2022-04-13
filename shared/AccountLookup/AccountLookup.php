<?php

namespace Shared\AccountLookup;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\GameIdentifierType;
use App\Entities\Repositories\MinecraftPlayerRepository;
use Shared\AccountLookup\Entities\PlayerIdentifier;
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
    public function find(PlayerIdentifier $identifier): ?Account
    {
        switch ($identifier->gameIdentifierType) {
            case GameIdentifierType::MINECRAFT_UUID:
                $uuid = str_replace(search: '-', replace: '', subject: $identifier->key);
                $player = $this->minecraftPlayerRepository->getByUuid($uuid);
                break;
        }

        if ($player === null) {
            throw new PlayerNotFoundException();
        }

        return $player->getLinkedAccount()
            ?? throw new NoLinkedAccountException();
    }
}
