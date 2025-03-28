<?php

namespace App\Core\Data\Exceptions;

/**
 * Authorisation required but failed and/or has not
 * been provided.
 */
class UnauthorisedException extends BaseHttpException
{
    protected int $status = 401;
}
