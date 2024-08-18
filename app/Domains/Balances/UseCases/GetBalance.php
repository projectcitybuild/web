<?php

namespace App\Domains\Balances\UseCases;

use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Exceptions\NoLinkedAccountException;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;
use Shared\PlayerLookup\Service\PlayerLookup;

/**
 * @final
 */
class GetBalance
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
    ) {
    }

    /**
     * @throws PlayerNotFoundException if identifier does not match any player
     * @throws NoLinkedAccountException if player is not linked to a PCB account
     */
    public function execute(PlayerIdentifier $identifier): int
    {
        $player = $this->playerLookup->find($identifier)
            ?? throw new PlayerNotFoundException();

        $account = $player->getLinkedAccount()
            ?? throw new NoLinkedAccountException();

        return $account->balance;
    }
}
