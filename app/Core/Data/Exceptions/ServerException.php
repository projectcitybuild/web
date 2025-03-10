<?php

namespace App\Core\Data\Exceptions;

/**
 * Server-side error, programming error and other errors
 * that weren't caused by a client.
 */
class ServerException extends BaseHttpException
{
    protected int $status = 500;
}
