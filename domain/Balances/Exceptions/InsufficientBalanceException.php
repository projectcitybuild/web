<?php

namespace Domain\Balances\Exceptions;

use App\Exceptions\Http\PredefinedHttpException;

final class InsufficientBalanceException extends PredefinedHttpException
{
    protected string $id = 'insufficient_balance';

    protected string $errorMessage = 'Cannot deduct more than the player\'s balance';

    protected int $status = 400;
}
