<?php

namespace App\Core\Data\Exceptions;

class NotImplementedException extends PredefinedHttpException
{
    protected string $id = 'not_implemented';
    protected string $errorMessage = 'This feature is not implemented. Please report this error.';
    protected int $status = 503;
}
