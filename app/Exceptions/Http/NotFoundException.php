<?php

namespace App\Exceptions\Http;

/**
 * A resource does not exist.
 */
class NotFoundException extends BaseHttpException
{
    protected int $status = 404;
}
