<?php

namespace App\Core\Exceptions;

/**
 * A resource does not exist.
 */
class NotFoundException extends BaseHttpException
{
    protected int $status = 404;
}
