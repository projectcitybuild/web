<?php
namespace App\Shared\Exceptions;

/**
 * A resource does not exist
 */
class NotFoundException extends BaseException {

    public function getStatusCode() : int {
        return 404;
    }

}