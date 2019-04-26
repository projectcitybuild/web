<?php
namespace App\Services\PlayerBans\Exceptions;

use App\Exceptions\Http\BadRequestException;

class UserAlreadyBannedException extends BadRequestException {}
