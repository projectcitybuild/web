<?php

namespace App\Exceptions\Http;

/**
 * Too many requests sent in a period of time.
 */
class TooManyRequestsException extends BaseHttpException
{
    protected int $status = 429;
}
