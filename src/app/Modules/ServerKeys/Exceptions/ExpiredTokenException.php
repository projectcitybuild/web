<?php
namespace App\Modules\ServerKeys\Exceptions;

use App\Shared\Exceptions\ForbiddenException;

class ExpiredTokenException extends ForbiddenException {}