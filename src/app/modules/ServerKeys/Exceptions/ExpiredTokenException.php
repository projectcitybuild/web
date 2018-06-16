<?php
namespace App\Modules\ServerKeys\Exceptions;

use App\Core\Exceptions\ForbiddenException;

class ExpiredTokenException extends ForbiddenException {}