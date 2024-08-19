<?php

namespace Repositories;

use App\Models\BalanceTransaction;

/**
 * @deprecated
 */
class BalanceHistoryRepository
{
    public function create(
        int $accountId,
        int $balanceBefore,
        int $balanceAfter,
        int $transactionAmount,
        string $reason,
    ) {
        BalanceTransaction::create([
            'account_id' => $accountId,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'transaction_amount' => $transactionAmount,
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}
