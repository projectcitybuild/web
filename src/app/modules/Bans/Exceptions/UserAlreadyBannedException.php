<?php
namespace App\Modules\Bans\Exceptions;

use App\Core\Exceptions\BadRequestException;

class UserAlreadyBannedException extends BadRequestException {}