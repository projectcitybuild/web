<?php
namespace App\Modules\ServerKeys\Exceptions;

use App\core\Exceptions\ForbiddenException;

class ExpiredTokenException extends ForbiddenException {}