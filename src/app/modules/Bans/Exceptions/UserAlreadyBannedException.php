<?php
namespace App\Modules\Bans\Exceptions;

use App\Support\Exceptions\BadRequestException;

class UserAlreadyBannedException extends BadRequestException {}