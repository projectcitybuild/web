<?php

namespace Domain\Balances\UseCases;

use App\Entities\Models\GameIdentifierType;
use Shared\AccountLookup\AccountLookup;
use Shared\AccountLookup\Entities\PlayerIdentifier;
use Shared\AccountLookup\Exceptions\NoLinkedAccountException;
use Shared\AccountLookup\Exceptions\PlayerNotFoundException;

/**
 * @final
 */
class DeductBalanceUseCase
{
    public function __construct(
        private AccountLookup $accountLookup,
    ) {}

    /**
     * @throws PlayerNotFoundException if identifier does not match any player
     * @throws NoLinkedAccountException if player is not linked to a PCB account
     */
    public function execute(
        PlayerIdentifier $identifier,
    ): int
    {
        $account = $this->accountLookup->find($identifier);
        $balance = $account->balance;

        if ()
    }
}
