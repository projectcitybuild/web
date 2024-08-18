<?php

namespace Shared\PlayerLookup\Exceptions;

use App\Core\Exceptions\PredefinedHttpException;

final class InvalidMinecraftUUIDException extends PredefinedHttpException
{
    protected string $id = 'invalid_minecraft_uuid';
    protected string $errorMessage = 'The given UUID is not valid';
    protected int $status = 400;
}
