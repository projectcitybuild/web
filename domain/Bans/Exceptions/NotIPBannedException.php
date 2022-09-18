<?php

namespace Domain\Bans\Exceptions;

use App\Exceptions\Http\PredefinedHttpException;

final class NotIPBannedException extends PredefinedHttpException
{
    protected string $id = 'ip_not_banned';
    protected string $errorMessage = 'This IP is not currently banned';
    protected int $status = 400;
}
