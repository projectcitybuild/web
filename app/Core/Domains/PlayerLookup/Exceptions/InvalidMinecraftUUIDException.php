<?php

namespace App\Core\Domains\PlayerLookup\Exceptions;

use App\Core\Data\Exceptions\PredefinedHttpException;

final class InvalidMinecraftUUIDException extends PredefinedHttpException
{
    protected string $id = 'invalid_minecraft_uuid';
    protected string $errorMessage = 'The given UUID is not valid';
    protected int $status = 400;
}
