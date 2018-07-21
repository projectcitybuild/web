<?php
namespace App\Modules\ServerKeys\Exceptions;

use App\Support\Exceptions\ForbiddenException;

class ExpiredTokenException extends ForbiddenException
{
}
