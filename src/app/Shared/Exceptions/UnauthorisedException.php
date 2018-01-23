<?php
namespace App\Shared\Exceptions;

/**
 * Authorisation required but failed and/or has not
 * been provided.
 */
class UnauthorisedException extends BaseException {

    public function getStatusCode() : int {
        return 401;
    }

}