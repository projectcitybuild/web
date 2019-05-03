<?php
namespace App\Services\PlayerBans\Exceptions;

use App\Exceptions\Http\BadRequestException;

final class UserAlreadyBannedException extends BadRequestException {}
