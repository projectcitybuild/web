<?php
namespace App\Shared\Exceptions;

/**
 * Server cannot or will not process the request due
 * to a client error. For example, bad input given,
 * malformed syntax, file size too big, etc.
 */
class BadRequestException extends BaseException {
    
    public function getStatusCode() : int {
        return 400;
    }

}