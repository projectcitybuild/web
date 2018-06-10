<?php
namespace App\core\Exceptions;

/**
 * A resource does not exist
 */
class NotFoundException extends BaseHttpException {

    protected $status = 404;

}