<?php

namespace Shared\AccountLookup\Exceptions;

use App\Exceptions\Http\PredefinedHttpException;

final class NoLinkedAccountException extends PredefinedHttpException
{
    protected string $id = 'no_linked_account';

    protected string $errorMessage = 'Player is not linked to a PCB account';

    protected int $status = 404;
}
