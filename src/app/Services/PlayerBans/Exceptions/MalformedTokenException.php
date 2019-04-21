<?php
namespace App\Services\PlayerBans\Exceptions;

use App\Exceptions\Http\BadRequestException;

class MalformedTokenException extends BadRequestException {}
