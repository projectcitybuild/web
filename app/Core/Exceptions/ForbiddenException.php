<?php

namespace App\Core\Exceptions;

/**
 * Request was valid but the server refuses action.
 * For example, a user who has no access to a resource.
 */
class ForbiddenException extends BaseHttpException
{
    protected int $status = 403;
}
