<?php

namespace App\Domains\Bans\Exceptions;

use App\Core\Exceptions\PredefinedHttpException;

final class NotIPBannedException extends PredefinedHttpException
{
    protected string $id = 'ip_not_banned';
    protected string $errorMessage = 'This IP address is not currently banned';
    protected int $status = 400;
}
