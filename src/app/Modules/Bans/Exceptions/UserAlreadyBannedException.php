<?php
namespace App\Modules\Bans\Exceptions;

use App\Shared\Exceptions\BadRequestException;

class UserAlreadyBannedException extends BadRequestException {}