<?php

namespace App\Domains\Balances\UseCases;

use App\Domains\Balances\Exceptions\InsufficientBalanceException;
use Exception;
use Illuminate\Support\Facades\DB;
use Repositories\BalanceHistoryRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Exceptions\NoLinkedAccountException;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;
use Shared\PlayerLookup\Service\PlayerLookup;

/**
 * @final
 */
class DeductBalance
{
    public function __construct(
        private readonly PlayerLookup $playerLookup,
        private readonly BalanceHistoryRepository $balanceHistoryRepository,
    ) {
    }

    /**
     * @throws PlayerNotFoundException if identifier does not match any player
     * @throws NoLinkedAccountException if player is not linked to a PCB account
     * @throws InsufficientBalanceException if account does not have enough currency
     * @throws Exception if amount is not greater than 0
     */
    public function execute(
        PlayerIdentifier $identifier,
        int $amount,
        string $reason,
    ) {
        if ($amount <= 0) {
            throw new Exception('Deduction amount must be greater than 0');
        }

        $player = $this->playerLookup->find($identifier)
            ?? throw new PlayerNotFoundException();

        $account = $player->getLinkedAccount()
            ?? throw new NoLinkedAccountException();

        $balance = $account->balance;
        $newBalance = $balance - $amount;

        if ($newBalance < 0) {
            throw new InsufficientBalanceException();
        }

        DB::beginTransaction();
        try {
            $this->balanceHistoryRepository->create(
                accountId: $account->getKey(),
                balanceBefore: $balance,
                balanceAfter: $newBalance,
                transactionAmount: -$amount,
                reason: $reason,
            );

            $account->balance = $newBalance;
            $account->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
