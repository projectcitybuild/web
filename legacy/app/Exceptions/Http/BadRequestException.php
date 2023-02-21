<?php

namespace App\Exceptions\Http;

/**
 * Server cannot or will not process the request due
 * to a client error. For example, bad input given,
 * malformed syntax, file size too big, etc.
 */
class BadRequestException extends BaseHttpException
{
    protected int $status = 400;
}
