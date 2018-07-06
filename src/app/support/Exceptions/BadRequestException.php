<?php
namespace App\Support\Exceptions;

/**
 * Server cannot or will not process the request due
 * to a client error. For example, bad input given,
 * malformed syntax, file size too big, etc.
 */
class BadRequestException extends BaseHttpException {
    
    protected $status = 400;

}