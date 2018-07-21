<?php
namespace Application\Modules\ServerKeys\Exceptions;

use Application\Exceptions\ForbiddenException;

class ExpiredTokenException extends ForbiddenException
{
}
