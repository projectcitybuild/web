<?php
namespace App\Modules\ServerKeys\Exceptions;

class InvalidTokenException extends \Exception {
    /**
     * Provided token has been blacklisted
     */
    const EXPIRED_TOKEN = 1;

    /**
     * Provided token is not in the correct format
     */
    const MALFORMED_TOKEN = 2;

    /**
     * Provided token does not belong to a key
     */
    const NO_SERVER_KEY = 3;

}