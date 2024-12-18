<?php

namespace App\Domains\Bans\Exceptions;

use App\Core\Data\Exceptions\PredefinedHttpException;

final class AlreadyIPBannedException extends PredefinedHttpException
{
    protected string $id = 'ip_already_banned';
    protected string $errorMessage = 'IP address is already banned';
    protected int $status = 400;
}
