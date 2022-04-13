<?php

namespace Domain\Balances\UseCases;

use App\Entities\Models\GameIdentifierType;
use Shared\AccountLookup\AccountLookup;
use Shared\AccountLookup\Exceptions\NoLinkedAccountException;
use Shared\AccountLookup\Exceptions\PlayerNotFoundException;

/**
 * @final
 */
class GetBalanceUseCase
{
    public function __construct(
        private AccountLookup $accountLookup,
    ) {}

    /**
     * @throws PlayerNotFoundException if identifier does not match any player
     * @throws NoLinkedAccountException if player is not linked to a PCB account
     */
    public function execute(string $uuid): int
    {
        $uuid = str_replace(search: '-', replace: '', subject: $uuid);

        $account = $this->accountLookup->find(
            identifier: $uuid,
            identifierType: GameIdentifierType::MINECRAFT_UUID,
        );

        return $account->balance;
    }
}
