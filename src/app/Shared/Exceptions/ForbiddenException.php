<?php
namespace App\Shared\Exceptions;

/**
 * Request was valid but the server refuses action.
 * For example, a user who has no access to a resource
 */
class ForbiddenException extends BaseException {

    public function getStatusCode() : int {
        return 403;
    }

}