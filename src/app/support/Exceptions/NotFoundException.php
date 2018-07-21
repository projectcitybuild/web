<?php
namespace App\Support\Exceptions;

/**
 * A resource does not exist
 */
class NotFoundException extends BaseHttpException
{
    protected $status = 404;
}
