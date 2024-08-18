<?php

namespace Shared\PlayerLookup\Exceptions;

use App\Core\Data\Exceptions\PredefinedHttpException;

class NonCreatableIdentifierException extends PredefinedHttpException
{
    protected string $id = 'non_creatable_identifier_exception';
    protected string $errorMessage = 'This player is not known and cannot be created';
    protected int $status = 400;
}
