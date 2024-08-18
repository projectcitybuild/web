<?php

namespace App\Domains\Balances\Exceptions;

use App\Core\Exceptions\PredefinedHttpException;

final class InsufficientBalanceException extends PredefinedHttpException
{
    protected string $id = 'insufficient_balance';
    protected string $errorMessage = 'Cannot deduct more than the player\'s balance';
    protected int $status = 400;
}
